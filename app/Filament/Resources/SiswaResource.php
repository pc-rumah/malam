<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Siswa;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\SiswaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\SiswaResource\RelationManagers;

class SiswaResource extends Resource
{
    protected static ?string $model = Siswa::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->required()
                    ->relationship(
                        name: 'user',
                        titleAttribute: 'name',
                        modifyQueryUsing: function ($query) {
                            $query->whereHas('roles', function ($q) {
                                $q->where('name', 'siswa'); #filter untuk role siswa saja
                            })
                                ->whereNotIn('id', Siswa::pluck('user_id')->toArray()); #filter agar user yang sudah dipakai tidak muncul
                        }
                    )
                    ->live()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $nama = User::find($state)?->name ?? null;
                        $set('nama', $nama);
                    }),
                Forms\Components\TextInput::make('nis')
                    ->required(),
                Forms\Components\TextInput::make('nama')
                    ->required()
                    ->readOnly(),
                Forms\Components\Select::make('kelas_id')
                    ->required()
                    ->relationship('kelas', 'nama_kelas'),
                Forms\Components\Select::make('jurusan_id')
                    ->required()
                    ->relationship('jurusan', 'nama_jurusan'),
                Forms\Components\TextInput::make('no_hp'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nis')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kelas.nama_kelas')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('jurusan.nama_jurusan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('no_hp')
                    ->searchable(),
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
            'index' => Pages\ListSiswas::route('/'),
            'create' => Pages\CreateSiswa::route('/create'),
            'edit' => Pages\EditSiswa::route('/{record}/edit'),
        ];
    }
}
