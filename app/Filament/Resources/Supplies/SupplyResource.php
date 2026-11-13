<?php

namespace App\Filament\Resources\Supplies;

use App\Filament\Resources\Supplies\Pages\CreateSupply;
use App\Filament\Resources\Supplies\Pages\EditSupply;
use App\Filament\Resources\Supplies\Pages\ListSupplies;
use App\Filament\Resources\Supplies\Schemas\SupplyForm;
use App\Filament\Resources\Supplies\Tables\SuppliesTable;
use App\Models\Supply;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class SupplyResource extends Resource
{
    protected static ?string $model = Supply::class;

    protected static ?string $modelLabel = 'Расходник';

    protected static ?string $pluralModelLabel = 'Расходники';

    protected static ?string $navigationLabel = 'Расходники';

    protected static string|\UnitEnum|null $navigationGroup = 'Склад';

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'name_supplies';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArchiveBox;

    public static function form(Schema $schema): Schema
    {
        return SupplyForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SuppliesTable::configure($table);
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
            'index' => ListSupplies::route('/'),
            'create' => CreateSupply::route('/create'),
            'edit' => EditSupply::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $lowStockCount = Supply::query()->where('quantity', '<=', 10)->count();

        return $lowStockCount > 0 ? (string) $lowStockCount : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'danger';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Расходники с низким остатком (10 и меньше)';
    }
}
