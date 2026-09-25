<?php

namespace Database\Seeders;

use App\Models\UnitKerja;
use Illuminate\Database\Seeder;

class UnitKerjaSeeder extends Seeder
{
    public function run(): void
    {
        $units = [
            ['nama' => 'Bagian Umum', 'kode' => 'BU'],
            ['nama' => 'Bidang Keuangan', 'kode' => 'BK'],
            ['nama' => 'Bidang Perencanaan', 'kode' => 'BP'],
            ['nama' => 'Bidang Kepegawaian', 'kode' => 'BKPG'],
            ['nama' => 'Sekretariat', 'kode' => 'SEK'],
            ['nama' => 'Bidang LPSE', 'kode' => 'LPSE'],
            ['nama' => 'Bidang JF', 'kode' => 'JF'],
            ['nama' => 'Bidang PSDM', 'kode' => 'PSDM'],
            ['nama' => 'Bidang PBJ', 'kode' => 'PBJ'],
        ];

        foreach ($units as $unit) {
            UnitKerja::firstOrCreate(['kode' => $unit['kode']], $unit);
        }
    }
}
