<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenerimaanPajakResource\Pages;
use App\Filament\Resources\PenerimaanPajakResource\RelationManagers;
use App\Models\PenerimaanPajak;
use App\Models\TargetPajak;
use App\Models\TahunAnggaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Carbon\Carbon;

class PenerimaanPajakResource extends Resource
{
    protected static ?string $model = PenerimaanPajak::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Pajak';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Penerimaan Pajak';
    protected static ?string $pluralModelLabel = 'Penerimaan Pajak';
    protected static ?string $modelLabel = 'Penerimaan Pajak';

    public static function form(Form $form): Form
    {
        $tahunAnggaranAktif = TahunAnggaran::where('aktif', true)->first();
        
        return $form
            ->schema([
                Forms\Components\Select::make('target_pajak_id')
                    ->label('Jenis Pajak')
                    ->options(function () use ($tahunAnggaranAktif) {
                        if (!$tahunAnggaranAktif) {
                            return [];
                        }
                        
                        return TargetPajak::where('tahun_anggaran_id', $tahunAnggaranAktif->id)
                            ->get()
                            ->mapWithKeys(function ($targetPajak) {
                                return [$targetPajak->id => $targetPajak->kodeRekening->uraian . ' - ' . $targetPajak->kodeRekening->kode];
                            })
                            ->toArray();
                    })
                    ->searchable()
                    ->required(),
                    
                Forms\Components\Select::make('bulan_triwulan')
                    ->label('Bulan (Triwulan)')
                    ->options([
                        '1-1' => 'Januari (Triwulan I)',
                        '2-1' => 'Februari (Triwulan I)',
                        '3-1' => 'Maret (Triwulan I)',
                        '4-2' => 'April (Triwulan II)',
                        '5-2' => 'Mei (Triwulan II)',
                        '6-2' => 'Juni (Triwulan II)',
                        '7-3' => 'Juli (Triwulan III)',
                        '8-3' => 'Agustus (Triwulan III)',
                        '9-3' => 'September (Triwulan III)',
                        '10-4' => 'Oktober (Triwulan IV)',
                        '11-4' => 'November (Triwulan IV)',
                        '12-4' => 'Desember (Triwulan IV)',
                    ])
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $parts = explode('-', $state);
                            $bulan = (int)$parts[0];
                            $tahun = date('Y');
                            $tanggal = Carbon::createFromDate($tahun, $bulan, 15)->format('Y-m-d');
                            $set('tanggal_penerimaan', $tanggal);
                        }
                    }),
                
                Forms\Components\DatePicker::make('tanggal_penerimaan')
                    ->required()
                    ->default(now()),
                    
                Forms\Components\TextInput::make('nilai_penerimaan')
                    ->required()
                    ->numeric()
                    ->prefix('Rp')
                    ->default(0),
                    
                Forms\Components\Textarea::make('keterangan')
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('targetPajak.kodeRekening.kode')
                    ->label('Kode Rekening')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('targetPajak.kodeRekening.uraian')
                    ->label('Jenis Pajak')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('targetPajak.tahunAnggaran.tahun')
                    ->label('Tahun')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_penerimaan')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nilai_penerimaan')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tahun')
                    ->options(function () {
                        return TahunAnggaran::pluck('tahun', 'id')->toArray();
                    })
                    ->query(function ($query, array $data) {
                        if (isset($data['value'])) {
                            $query->whereHas('targetPajak', function ($query) use ($data) {
                                $query->where('tahun_anggaran_id', $data['value']);
                            });
                        }
                    }),
                Tables\Filters\Filter::make('tanggal_penerimaan')
                    ->form([
                        Forms\Components\DatePicker::make('dari_tanggal'),
                        Forms\Components\DatePicker::make('sampai_tanggal'),
                    ])
                    ->query(function ($query, array $data) {
                        if (isset($data['dari_tanggal'])) {
                            $query->where('tanggal_penerimaan', '>=', $data['dari_tanggal']);
                        }
                        
                        if (isset($data['sampai_tanggal'])) {
                            $query->where('tanggal_penerimaan', '<=', $data['sampai_tanggal']);
                        }
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
            'index' => Pages\ListPenerimaanPajaks::route('/'),
            'create' => Pages\CreatePenerimaanPajak::route('/create'),
            'edit' => Pages\EditPenerimaanPajak::route('/{record}/edit'),
        ];
    }
}
