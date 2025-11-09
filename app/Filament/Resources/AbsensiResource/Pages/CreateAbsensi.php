<?php

namespace App\Filament\Resources\AbsensiResource\Pages;

use Filament\Actions;
use App\Models\Absensi;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\AbsensiResource;

class CreateAbsensi extends CreateRecord
{
    protected static string $resource = AbsensiResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        foreach ($data['siswa_absen'] as $row) {
            \App\Models\Absensi::create([
                'jadwal_id' => $data['jadwal_id'],
                'siswa_id' => $row['siswa_id'],
                'tanggal' => now()->toDateString(),
                'status' => $row['status'],
            ]);
        }

        // ✅ Kembalikan dummy model supaya Filament tidak insert record kosong
        return new \App\Models\Absensi();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
