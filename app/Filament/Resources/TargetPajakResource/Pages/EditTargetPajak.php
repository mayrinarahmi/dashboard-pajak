<?php

namespace App\Filament\Resources\TargetPajakResource\Pages;

use App\Filament\Resources\TargetPajakResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTargetPajak extends EditRecord
{
    protected static string $resource = TargetPajakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
