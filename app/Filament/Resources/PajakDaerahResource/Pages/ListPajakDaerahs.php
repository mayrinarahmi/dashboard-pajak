<?php

namespace App\Filament\Resources\PajakDaerahResource\Pages;

use App\Filament\Resources\PajakDaerahResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPajakDaerahs extends ListRecords
{
    protected static string $resource = PajakDaerahResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
