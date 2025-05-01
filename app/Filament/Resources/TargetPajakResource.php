<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TargetPajakResource\Pages;
use App\Filament\Resources\TargetPajakResource\RelationManagers;
use App\Models\TargetPajak;
use App\Models\TahunAnggaran;
use App\Models\KodeRekening;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TargetPajakResource extends Resource
{
    protected static ?string $model = TargetPajak::class;
    protected static ?string $navigationIcon = 'heroicon-o-flag';
    protected static ?string $navigationGroup = 'Pajak';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Target Pajak';
    protected static ?string $pluralModelLabel = 'Target Pajak';
    protected static ?string $modelLabel = 'Target Pajak';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tahun_anggaran_id')
                    ->relationship('tahunAnggaran', 'tahun')
                    ->required(),
                Forms\Components\Select::make('kode_rekening_id')
                    ->label('Jenis Pajak')
                    ->options(function () {
                        return KodeRekening::where('is_pajak', true)
                            ->orderBy('kode')
                            ->get()
                            ->pluck('uraian', 'id')
                            ->toArray();
                    })
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('pagu_anggaran')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tahunAnggaran.tahun')
                    ->sortable(),
                Tables\Columns\TextColumn::make('kodeRekening.kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kodeRekening.uraian')
                    ->label('Jenis Pajak')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pagu_anggaran')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nilai_target')
                    ->money('IDR')
                    ->label('Target Sekarang')
                    ->tooltip(function (TargetPajak $record) {
                        $bulan = now()->month;
                        $persentase = $record->tahunAnggaran->getTargetKumulatifSampai($bulan);
                        return "Berdasarkan target {$persentase}% (kumulatif sampai bulan ini)";
                    }),
                Tables\Columns\TextColumn::make('total_penerimaan')
                    ->money('IDR')
                    ->label('Realisasi')
                    ->sortable(),
                Tables\Columns\TextColumn::make('persentase_penerimaan')
                    ->label('Realisasi (%)')
                    ->suffix('%')
                    ->numeric(2)
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tahun_anggaran_id')
                    ->relationship('tahunAnggaran', 'tahun')
                    ->label('Tahun Anggaran'),
                Tables\Filters\Filter::make('target_tercapai')
                    ->toggle()
                    ->label('Target Tercapai')
                    ->query(function ($query) {
                        return $query->whereRaw('(SELECT SUM(nilai_penerimaan) FROM penerimaan_pajaks WHERE penerimaan_pajaks.target_pajak_id = target_pajaks.id) >= (target_pajaks.pagu_anggaran * (SELECT SUM(persentase_target) FROM target_kelompoks WHERE target_kelompoks.tahun_anggaran_id = target_pajaks.tahun_anggaran_id) / 100)');
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTargetPajaks::route('/'),
            'create' => Pages\CreateTargetPajak::route('/create'),
            'edit' => Pages\EditTargetPajak::route('/{record}/edit'),
        ];
    }
}
