<?php

namespace App\Filament\Resources\PenerimaanPajakResource\Pages;

use App\Filament\Resources\PenerimaanPajakResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenerimaanPajaks extends ListRecords
{
    protected static string $resource = PenerimaanPajakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
