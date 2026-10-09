<?php

namespace App\Filament\Resources\TypeEquipment;

use App\Filament\Resources\TypeEquipment\Pages\CreateTypeEquipment;
use App\Filament\Resources\TypeEquipment\Pages\EditTypeEquipment;
use App\Filament\Resources\TypeEquipment\Pages\ListTypeEquipment;
use App\Filament\Resources\TypeEquipment\Schemas\TypeEquipmentForm;
use App\Filament\Resources\TypeEquipment\Tables\TypeEquipmentTable;
use App\Models\TypeEquipment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TypeEquipmentResource extends Resource
{
    protected static ?string $model = TypeEquipment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return TypeEquipmentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TypeEquipmentTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTypeEquipment::route('/'),
            'create' => CreateTypeEquipment::route('/create'),
            'edit' => EditTypeEquipment::route('/{record}/edit'),
        ];
    }
}
