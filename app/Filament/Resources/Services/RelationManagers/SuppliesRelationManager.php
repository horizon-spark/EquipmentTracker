<?php

namespace App\Filament\Resources\Services\RelationManagers;

use App\Models\Supply;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuppliesRelationManager extends RelationManager
{
    protected static string $relationship = 'supplies';

    protected static ?string $title = 'Расходники';

    protected static ?string $recordTitleAttribute = 'name_supplies';

    /**
     * Форма для РЕДАКТИРОВАНИЯ pivot-записи.
     * Здесь только quantity_used — расходник менять нельзя.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('quantity_used')
                    ->label('Израсходовано')
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->default(1)
                    ->required()
                    ->validationMessages([
                        'min' => 'Количество должно быть не меньше :min.',
                        'min.numeric' => 'Количество должно быть не меньше :min.',
                    ])
                    ->helperText('Увеличение количества спишет разницу со склада, уменьшение — вернёт её обратно.'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name_supplies')
                    ->label('Расходник')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pivot.quantity_used')
                    ->label('Израсходовано')
                    ->numeric()
                    ->sortable(),

                TextColumn::make('quantity')
                    ->label('Остаток на складе')
                    ->numeric()
                    ->sortable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Добавить расходник')
                    ->modalHeading('Добавить расходник')
                    ->modalSubmitActionLabel('Добавить')
                    ->recordSelect(fn (Select $select): Select => $select
                        ->label('Расходник')
                        ->options(fn (): array => $this->availableSupplyOptions())
                        ->searchable()
                        ->live()
                        ->required()
                    )
                    ->modalDescription('Остаток на складе уменьшится на указанное количество.')
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('quantity_used')
                            ->label('Израсходовано')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(fn (Get $get): ?int => $this->availableStock($get('recordId')))
                            ->default(1)
                            ->required()
                            ->validationMessages([
                                'min' => 'Количество должно быть не меньше :min.',
                                'min.numeric' => 'Количество должно быть не меньше :min.',
                                'max' => 'Количество не может превышать остаток на складе (:max).',
                                'max.numeric' => 'Количество не может превышать остаток на складе (:max).',
                            ])
                            ->helperText(fn (Get $get): ?string => $this->availableStockLabel($get('recordId'))),
                    ]),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Изменить количество')
                    ->modalHeading('Изменить количество')
                    ->modalDescription('Разница будет списана со склада или возвращена на склад.')
                    ->modalSubmitActionLabel('Сохранить'),
                DetachAction::make()
                    ->label('Убрать')
                    ->modalHeading('Убрать расходник')
                    ->modalDescription('Израсходованное количество вернётся на склад.')
                    ->modalSubmitActionLabel('Убрать'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return array<int, string>
     */
    private function availableSupplyOptions(): array
    {
        $attachedSupplyIds = $this->getOwnerRecord()
            ->supplies()
            ->pluck('supplies.id_supply');

        return Supply::query()
            ->where('quantity', '>', 0)
            ->whereNotIn('id_supply', $attachedSupplyIds)
            ->orderBy('name_supplies')
            ->get()
            ->mapWithKeys(fn (Supply $supply): array => [
                $supply->id_supply => "{$supply->name_supplies} (остаток: {$supply->quantity})",
            ])
            ->all();
    }

    private function availableStock(mixed $supplyId): ?int
    {
        if (! is_numeric($supplyId)) {
            return null;
        }

        $quantity = Supply::query()->whereKey((int) $supplyId)->value('quantity');

        return $quantity === null ? null : (int) $quantity;
    }

    private function availableStockLabel(mixed $supplyId): ?string
    {
        $quantity = $this->availableStock($supplyId);

        return $quantity === null ? null : "Доступно на складе: {$quantity}";
    }
}
