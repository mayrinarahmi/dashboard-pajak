<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TargetKelompokResource\Pages;
use App\Filament\Resources\TargetKelompokResource\RelationManagers;
use App\Models\TargetKelompok;
use App\Models\BulanTerkait;
use App\Models\TahunAnggaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TargetKelompokResource extends Resource
{
    protected static ?string $model = TargetKelompok::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Target Kelompok Bulan';
    protected static ?string $pluralModelLabel = 'Target Kelompok Bulan';
    protected static ?string $modelLabel = 'Target Kelompok Bulan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tahun_anggaran_id')
                    ->relationship('tahunAnggaran', 'tahun')
                    ->required(),
                
                Forms\Components\TextInput::make('nama_kelompok')
                    ->required()
                    ->maxLength(255),
                
                Forms\Components\CheckboxList::make('bulan_termasuk')
                    ->label('Bulan yang Termasuk')
                    ->options([
                        1 => 'Januari (Triwulan I)',
                        2 => 'Februari (Triwulan I)',
                        3 => 'Maret (Triwulan I)',
                        4 => 'April (Triwulan II)',
                        5 => 'Mei (Triwulan II)',
                        6 => 'Juni (Triwulan II)',
                        7 => 'Juli (Triwulan III)',
                        8 => 'Agustus (Triwulan III)',
                        9 => 'September (Triwulan III)',
                        10 => 'Oktober (Triwulan IV)',
                        11 => 'November (Triwulan IV)',
                        12 => 'Desember (Triwulan IV)',
                    ])
                    ->required()
                    ->columns(3),
                    
                Forms\Components\TextInput::make('persentase_target')
                    ->label('Persentase Target (%)')
                    ->required()
                    ->numeric()
                    ->suffix('%'),
                    
                Forms\Components\Textarea::make('keterangan')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tahunAnggaran.tahun')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_kelompok')
                    ->searchable(),
                Tables\Columns\TextColumn::make('bulan_list')
                    ->label('Bulan Termasuk'),
                Tables\Columns\TextColumn::make('persentase_target')
                    ->label('Target (%)')
                    ->suffix('%')
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
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('checkTotal')
                    ->label('Cek Total Persentase')
                    ->action(function () {
                        $tahunAnggaranAktif = TahunAnggaran::where('aktif', true)->first();
                        
                        if (!$tahunAnggaranAktif) {
                            Notification::make()
                                ->title('Tidak ada tahun anggaran aktif')
                                ->warning()
                                ->send();
                            return;
                        }
                        
                        $totalPersentase = TargetKelompok::where('tahun_anggaran_id', $tahunAnggaranAktif->id)
                            ->sum('persentase_target');
                            
                        if ($totalPersentase < 100) {
                            Notification::make()
                                ->title("Total target saat ini: {$totalPersentase}%")
                                ->warning()
                                ->body("Masih kurang " . (100 - $totalPersentase) . "% untuk mencapai 100%")
                                ->send();
                        } else if ($totalPersentase > 100) {
                            Notification::make()
                                ->title("Total target saat ini: {$totalPersentase}%")
                                ->danger()
                                ->body("Melebihi 100%. Silakan sesuaikan persentase target")
                                ->send();
                        } else {
                            Notification::make()
                                ->title("Total target: 100%")
                                ->success()
                                ->body("Persentase target sudah tepat")
                                ->send();
                        }
                    }),
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
            'index' => Pages\ListTargetKelompoks::route('/'),
            'create' => Pages\CreateTargetKelompok::route('/create'),
            'edit' => Pages\EditTargetKelompok::route('/{record}/edit'),
        ];
    }
    
    public static function afterSave(Model $record, array $data): void
    {
        // Hapus bulan_terkait yang ada untuk record ini
        $record->bulanTerkaits()->delete();
        
        // Buat bulan_terkait baru
        foreach ($data['bulan_termasuk'] as $bulan) {
            BulanTerkait::create([
                'target_kelompok_id' => $record->id,
                'bulan' => $bulan,
            ]);
        }
        
        // Validasi total persentase
        $totalPersentase = TargetKelompok::where('tahun_anggaran_id', $record->tahun_anggaran_id)
            ->sum('persentase_target');
            
        if ($totalPersentase > 100) {
            Notification::make()
                ->title('Peringatan: Total persentase melebihi 100%')
                ->warning()
                ->body("Total persentase saat ini adalah {$totalPersentase}%. Mohon sesuaikan agar total tidak melebihi 100%.")
                ->persistent()
                ->send();
        }
    }
}
