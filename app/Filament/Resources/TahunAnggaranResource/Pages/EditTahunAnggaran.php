<?php

namespace App\Filament\Resources\TahunAnggaranResource\Pages;

use App\Filament\Resources\TahunAnggaranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTahunAnggaran extends EditRecord
{
    protected static string $resource = TahunAnggaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
