<?php

namespace App\Filament\Resources\Services\RelationManagers;

use App\Models\Supply;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SuppliesRelationManager extends RelationManager
{
    protected static string $relationship = 'supplies';

    protected static ?string $title = 'Расходники';

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
                    ->minValue(1)
                    ->default(1)
                    ->required(),
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
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Добавить расходник')
                    ->preloadRecordSelect()
                    ->recordSelect(
                        fn (Select $select) => $select
                            ->label('Расходник')
                            ->options(Supply::pluck('name_supplies', 'id_supply'))
                            ->searchable()
                            ->required()
                    )
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect(),
                        TextInput::make('quantity_used')
                            ->label('Израсходовано')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        // сюда можно добавить логику — например, списание со склада
                        return $data;
                    }),
            ])
            ->recordActions([
                EditAction::make()
                    ->label('Изменить количество'),
                DetachAction::make()
                    ->label('Убрать'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}