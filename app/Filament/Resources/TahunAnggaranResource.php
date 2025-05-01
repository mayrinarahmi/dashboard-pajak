<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TahunAnggaranResource\Pages;
use App\Filament\Resources\TahunAnggaranResource\RelationManagers;
use App\Models\TahunAnggaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TahunAnggaranResource extends Resource
{
    protected static ?string $model = TahunAnggaran::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 1;
    protected static ?string $navigationLabel = 'Tahun Anggaran';
    protected static ?string $pluralModelLabel = 'Tahun Anggaran';
    protected static ?string $modelLabel = 'Tahun Anggaran';

    public static function form(Form $form): Form
    {
        return $form
->schema([
                Forms\Components\TextInput::make('tahun')
                    ->required()
                    ->numeric()
                    ->default(date('Y')),
                Forms\Components\DatePicker::make('tanggal_mulai')
                    ->required()
                    ->default(now()->startOfYear()),
                Forms\Components\DatePicker::make('tanggal_selesai')
                    ->required()
                    ->default(now()->endOfYear()),
                Forms\Components\Toggle::make('aktif')
                    ->default(false)
                    ->afterStateUpdated(function ($state, $set, callable $get) {
                        if ($state) {
                            // Nonaktifkan tahun anggaran lain
                            TahunAnggaran::where('id', '!=', $get('id'))
                                ->where('aktif', true)
                                ->update(['aktif' => false]);
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tahun')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tanggal_mulai')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_selesai')
                    ->date()
                    ->sortable(),
                Tables\Columns\IconColumn::make('aktif')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('setupTargetKelompok')
                    ->label('Setup Target Kelompok')
                    ->icon('heroicon-o-cog')
                    ->action(function (TahunAnggaran $record) {
                        return redirect()->route('filament.admin.resources.target-kelompoks.index', [
                            'tableFilters[tahun_anggaran_id][value]' => $record->id,
                        ]);
                    }),
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
            'index' => Pages\ListTahunAnggarans::route('/'),
            'create' => Pages\CreateTahunAnggaran::route('/create'),
            'edit' => Pages\EditTahunAnggaran::route('/{record}/edit'),
        ];
    }
}
