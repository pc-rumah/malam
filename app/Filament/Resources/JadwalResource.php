<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Guru;
use Filament\Tables;
use App\Models\Jadwal;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\JadwalResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\JadwalResource\RelationManagers;
use App\Models\Matapelajaran;

class JadwalResource extends Resource
{
    protected static ?string $model = Jadwal::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('mata_pelajaran_id')
                    ->required()
                    ->relationship('mapel', 'nama_mapel')
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $guru = Matapelajaran::find($state)?->guru_id ?? null;
                        $set('guru_id', $guru);
                    }),
                Forms\Components\Select::make('kelas_id')
                    ->required()
                    ->relationship('kelas', 'nama_kelas'),
                Forms\Components\Select::make('guru_id')
                    ->label('Nama Guru')
                    ->relationship('guru', 'nama')
                    ->disabled()
                    ->dehydrated()
                    ->required(),
                Forms\Components\Select::make('hari')
                    ->options([
                        'senin' => 'Senin',
                        'selasa' => 'Selasa',
                        'rabu' => 'Rabu',
                        'kamis' => 'Kamis',
                        'jumat' => 'Jumat',
                        'sabtu' => 'Sabtu',
                    ]),
                Forms\Components\TimePicker::make('jam_mulai')
                    ->required()
                    ->seconds(false),
                Forms\Components\TimePicker::make('jam_selesai')
                    ->required()
                    ->seconds(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mapel.nama_mapel')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('guru.nama')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('hari')
                    ->searchable(),
                Tables\Columns\TextColumn::make('jam_mulai'),
                Tables\Columns\TextColumn::make('jam_selesai'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListJadwals::route('/'),
            'create' => Pages\CreateJadwal::route('/create'),
            'edit' => Pages\EditJadwal::route('/{record}/edit'),
        ];
    }
}
