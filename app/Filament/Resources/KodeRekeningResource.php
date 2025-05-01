<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KodeRekeningResource\Pages;
use App\Filament\Resources\KodeRekeningResource\RelationManagers;
use App\Models\KodeRekening;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class KodeRekeningResource extends Resource
{
    protected static ?string $model = KodeRekening::class;
    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Kode Rekening';
    protected static ?string $pluralModelLabel = 'Kode Rekening';
    protected static ?string $modelLabel = 'Kode Rekening';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('kode')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('uraian')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('parent_id')
                    ->label('Parent')
                    ->relationship('parent', 'uraian', function ($query) {
                        return $query->orderBy('kode');
                    })
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->afterStateUpdated(function ($state, $set) {
                        if ($state) {
                            $parent = KodeRekening::find($state);
                            if ($parent) {
                                $set('level', $parent->level + 1);
                            }
                        } else {
                            $set('level', 1);
                        }
                    }),
                Forms\Components\TextInput::make('level')
                    ->required()
                    ->numeric()
                    ->default(1)
                    ->disabled()
                    ->dehydrated(),
                Forms\Components\Toggle::make('is_pajak')
                    ->label('Apakah Pajak?')
                    ->required()
                    ->default(false)
                    ->reactive()
                    ->disabled(fn (callable $get) => $get('level') < 4)
                    ->helperText('Hanya level 4 yang bisa diset sebagai pajak')
                    ->afterStateUpdated(function ($state, $set, callable $get) {
                        if ($state && $get('level') < 4) {
                            $set('is_pajak', false);
                        }
                    }),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('uraian')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('parent.uraian')
                    ->label('Parent')
                    ->sortable(),
                Tables\Columns\TextColumn::make('level')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_pajak')
                    ->label('Pajak?')
                    ->boolean(),
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
                Tables\Filters\Filter::make('level')
                    ->form([
                        Forms\Components\Select::make('level')
                            ->options([
                                1 => 'Level 1',
                                2 => 'Level 2',
                                3 => 'Level 3',
                                4 => 'Level 4',
                            ]),
                    ])
                    ->query(function ($query, array $data) {
                        if (isset($data['level'])) {
                            $query->where('level', $data['level']);
                        }
                    }),

Tables\Filters\Filter::make('is_pajak')
                    ->toggle()
                    ->label('Hanya Pajak')
                    ->query(fn ($query) => $query->where('is_pajak', true)),
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
            'index' => Pages\ListKodeRekenings::route('/'),
            'create' => Pages\CreateKodeRekening::route('/create'),
            'edit' => Pages\EditKodeRekening::route('/{record}/edit'),
        ];
    }
}
    

