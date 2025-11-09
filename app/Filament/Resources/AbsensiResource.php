<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Guru;
use Filament\Tables;
use App\Models\Siswa;
use App\Models\Jadwal;
use App\Models\Absensi;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AbsensiResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AbsensiResource\RelationManagers;

class AbsensiResource extends Resource
{
    protected static ?string $model = Absensi::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Pilih Jadwal')
                    ->schema([
                        Forms\Components\Select::make('jadwal_id')
                            ->label('Jadwal Mengajar')
                            ->dehydrated(true)
                            ->options(function () {
                                $guru = Guru::where('user_id', Filament::auth()->id())->first();
                                if (!$guru) return [];

                                return Jadwal::with(['mapel', 'kelas'])
                                    ->where('guru_id', $guru->id)
                                    ->get()
                                    ->mapWithKeys(fn($j) => [
                                        $j->id => "{$j->hari} - {$j->mapel->nama_mapel} ({$j->kelas->nama_kelas})"
                                    ]);
                            })
                            ->reactive()
                            ->searchable()
                            ->required()
                            ->afterStateUpdated(function (callable $set, $state) {
                                $jadwal = \App\Models\Jadwal::with('kelas')->find($state);

                                if (!$jadwal) {
                                    $set('siswa_absen', []);
                                    return;
                                }

                                $siswaList = \App\Models\Siswa::where('kelas_id', $jadwal->kelas_id)
                                    ->get()
                                    ->map(fn($s) => [
                                        'siswa_id' => $s->id,
                                        'nama' => $s->nama,
                                        'status' => 'hadir',
                                    ])
                                    ->toArray();

                                $set('siswa_absen', $siswaList);
                            }),
                    ]),

                Forms\Components\Section::make('Daftar Kehadiran')
                    ->description('Centang dan ubah status kehadiran siswa.')
                    ->schema([
                        Forms\Components\Repeater::make('siswa_absen')
                            ->label('')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Hidden::make('siswa_id'),
                                Forms\Components\TextInput::make('nama')
                                    ->disabled()
                                    ->dehydrated(false),
                                Forms\Components\Select::make('status')
                                    ->options([
                                        'hadir' => 'Hadir',
                                        'izin' => 'Izin',
                                        'sakit' => 'Sakit',
                                        'alfa' => 'Alfa',
                                    ])
                                    ->default('hadir'),
                            ])
                            ->visible(fn(callable $get) => filled($get('jadwal_id')))
                            ->default(function (callable $get) {
                                $jadwal = Jadwal::find($get('jadwal_id'));
                                if (!$jadwal) return [];

                                return Siswa::where('kelas_id', $jadwal->kelas_id)
                                    ->get()
                                    ->map(fn($s) => [
                                        'siswa_id' => $s->id,
                                        'nama' => $s->nama,
                                        'status' => 'hadir',
                                    ])->toArray();
                            }),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('jadwal.mapel.nama_mapel')->label('Mata Pelajaran'),
                Tables\Columns\TextColumn::make('jadwal.kelas.nama_kelas')->label('Kelas'),
                Tables\Columns\TextColumn::make('tanggal')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->searchable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'hadir' => 'success',
                        'izin' => 'warning',
                        'alfa' => 'danger',
                        'sakit' => 'gray',
                    }),
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
            'index' => Pages\ListAbsensis::route('/'),
            'create' => Pages\CreateAbsensi::route('/create'),
            'edit' => Pages\EditAbsensi::route('/{record}/edit'),
        ];
    }
}
