<?php

namespace App\Filament\Resources\TargetPajakResource\Pages;

use App\Filament\Resources\TargetPajakResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTargetPajaks extends ListRecords
{
    protected static string $resource = TargetPajakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
