<?php

namespace App\Filament\Resources\TargetKelompokResource\Pages;

use App\Filament\Resources\TargetKelompokResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTargetKelompoks extends ListRecords
{
    protected static string $resource = TargetKelompokResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
