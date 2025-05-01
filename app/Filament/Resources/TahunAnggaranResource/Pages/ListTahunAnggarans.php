<?php

namespace App\Filament\Resources\TahunAnggaranResource\Pages;

use App\Filament\Resources\TahunAnggaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTahunAnggarans extends ListRecords
{
    protected static string $resource = TahunAnggaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
