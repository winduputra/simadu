<?php

namespace Database\Seeders;

use App\Models\DocumentCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DocumentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['nama' => 'Surat Keputusan', 'warna' => '#3B82F6', 'ikon' => 'file-text'],
            ['nama' => 'Nota Dinas', 'warna' => '#8B5CF6', 'ikon' => 'file-text'],
            ['nama' => 'Laporan', 'warna' => '#10B981', 'ikon' => 'bar-chart'],
            ['nama' => 'Surat Masuk', 'warna' => '#F59E0B', 'ikon' => 'inbox'],
            ['nama' => 'Surat Keluar', 'warna' => '#EF4444', 'ikon' => 'send'],
            ['nama' => 'MoU', 'warna' => '#06B6D4', 'ikon' => 'handshake'],
            ['nama' => 'Kontrak', 'warna' => '#F97316', 'ikon' => 'file-signature'],
            ['nama' => 'Foto Kegiatan', 'warna' => '#EC4899', 'ikon' => 'image'],
            ['nama' => 'Lainnya', 'warna' => '#6B7280', 'ikon' => 'file'],
        ];

        foreach ($categories as $cat) {
            DocumentCategory::firstOrCreate(
                ['slug' => Str::slug($cat['nama'])],
                array_merge($cat, ['slug' => Str::slug($cat['nama'])])
            );
        }
    }
}
