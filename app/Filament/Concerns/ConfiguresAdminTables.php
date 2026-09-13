<?php

namespace App\Filament\Concerns;

use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Table;

trait ConfiguresAdminTables
{
    protected static function configureAdminListingTable(Table $table): Table
    {
        return $table
            ->filtersLayout(FiltersLayout::AboveContentCollapsible)
            ->filtersFormColumns(2)
            ->persistFiltersInSession()
            ->deferFilters(false)
            ->columnManager()
            ->persistColumnsInSession();
    }
}
