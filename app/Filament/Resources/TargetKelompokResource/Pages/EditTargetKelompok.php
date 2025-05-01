<?php

namespace App\Filament\Resources\TargetKelompokResource\Pages;

use App\Filament\Resources\TargetKelompokResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTargetKelompok extends EditRecord
{
    protected static string $resource = TargetKelompokResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
