<?php

namespace App\Filament\Resources\Services\Schemas;

use App\Models\Supply;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('id_equipment')
                    ->label('Оборудование')
                    ->relationship('equipment', 'inv_number')
                    ->searchable()
                    ->preload()
                    ->required(),

                DatePicker::make('service_date')
                    ->label('Дата обслуживания')
                    ->displayFormat('d.m.Y')
                    ->required(),

                Textarea::make('description')
                    ->label('Описание')
                    ->maxLength(255)
                    ->columnSpanFull(),

                Repeater::make('supplies')
                    ->label('Использованные расходники')
                    ->addActionLabel('Добавить расходник')
                    ->defaultItems(0)
                    ->schema([
                        Select::make('id_supply')
                            ->label('Расходник')
                            ->options(fn (): array => self::supplyOptions())
                            ->searchable()
                            ->live()
                            ->required()
                            ->distinct()
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->afterStateUpdated(fn (Set $set) => $set('quantity_used', 1)),

                        TextInput::make('quantity_used')
                            ->label('Израсходовано')
                            ->numeric()
                            ->integer()
                            ->minValue(1)
                            ->maxValue(fn (Get $get): ?int => self::stockFor($get('id_supply')))
                            ->default(1)
                            ->required()
                            ->validationMessages([
                                'min' => 'Количество должно быть не меньше :min.',
                                'min.numeric' => 'Количество должно быть не меньше :min.',
                                'max' => 'Количество не может превышать остаток на складе (:max).',
                                'max.numeric' => 'Количество не может превышать остаток на складе (:max).',
                            ])
                            ->helperText(fn (Get $get): ?string => self::stockLabel($get('id_supply'))),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->visible(fn (string $operation): bool => $operation === 'create')
                    ->dehydrated(fn (string $operation): bool => $operation === 'create'),
            ]);
    }

    /**
     * @return array<int, string>
     */
    private static function supplyOptions(): array
    {
        return Supply::query()
            ->where('quantity', '>', 0)
            ->orderBy('name_supplies')
            ->get()
            ->mapWithKeys(fn (Supply $supply): array => [
                $supply->id_supply => "{$supply->name_supplies} (остаток: {$supply->quantity})",
            ])
            ->all();
    }

    private static function stockFor(mixed $supplyId): ?int
    {
        if (! is_numeric($supplyId)) {
            return null;
        }

        $quantity = Supply::query()->whereKey((int) $supplyId)->value('quantity');

        return $quantity === null ? null : (int) $quantity;
    }

    private static function stockLabel(mixed $supplyId): ?string
    {
        $quantity = self::stockFor($supplyId);

        return $quantity === null ? null : "Доступно на складе: {$quantity}";
    }
}
