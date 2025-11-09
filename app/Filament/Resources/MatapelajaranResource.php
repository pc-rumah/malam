<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MatapelajaranResource\Pages;
use App\Filament\Resources\MatapelajaranResource\RelationManagers;
use App\Models\Matapelajaran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MatapelajaranResource extends Resource
{
    protected static ?string $model = Matapelajaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_mapel')
                    ->required(),
                Forms\Components\TextInput::make('kode_mapel'),
                Forms\Components\Select::make('guru_id')
                    ->required()
                    ->relationship('guru', 'nama'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_mapel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kode_mapel')
                    ->searchable(),
                Tables\Columns\TextColumn::make('guru.nama')
                    ->searchable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ListMatapelajarans::route('/'),
            'create' => Pages\CreateMatapelajaran::route('/create'),
            'edit' => Pages\EditMatapelajaran::route('/{record}/edit'),
        ];
    }
}
