<?php

namespace App\Filament\Resources\KodeRekeningResource\Pages;

use App\Filament\Resources\KodeRekeningResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKodeRekening extends EditRecord
{
    protected static string $resource = KodeRekeningResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
