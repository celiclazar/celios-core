<?php

namespace Celios\Core\Filament\Resources\Brands;

use BackedEnum;
use Celios\Core\Filament\Resources\Brands\Pages\CreateBrand;
use Celios\Core\Filament\Resources\Brands\Pages\EditBrand;
use Celios\Core\Filament\Resources\Brands\Pages\ListBrands;
use Celios\Core\Filament\Resources\Brands\Schemas\BrandForm;
use Celios\Core\Filament\Resources\Brands\Tables\BrandsTable;
use Celios\Core\Models\Brand;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): ?string
    {
        return __('sidebar.group_system');
    }

    public static function getNavigationLabel(): string
    {
        return __('sidebar.brands');
    }

    public static function getPluralModelLabel(): string
    {
        return __('sidebar.brands');
    }

    public static function getModelLabel(): string
    {
        return __('sidebar.brand_single');
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            $count = Brand::query()->where('is_active', true)->count();

            return $count > 0 ? (string) $count : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return BrandForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BrandsTable::configure($table);
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
            'index' => ListBrands::route('/'),
            'create' => CreateBrand::route('/create'),
            'edit' => EditBrand::route('/{record}/edit'),
        ];
    }
}
