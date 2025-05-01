<?php

namespace App\Filament\Resources\PenerimaanPajakResource\Pages;

use App\Filament\Resources\PenerimaanPajakResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenerimaanPajak extends EditRecord
{
    protected static string $resource = PenerimaanPajakResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
