<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenis = [
            "TWK (Tes Wawasan Kebangsaan)",
            "TIU (Tes Intelegensi Umum)",
            "TKP (Tes Karakteristik Pribadi)",
            "SKD (Soal Kompetensi Dasar)",
            "SKB (Soal Kompetensi Bidang)",
        ];

        $tipe = [
            'Simulasi',
            'Latihan',
            'Tryout',
        ];

        foreach ($jenis as $item) {
            \App\Models\JenisUjian::create([
                'nama' => $item,
            ]);
        }
        foreach ($tipe as $item) {
            \App\Models\TipeUjian::create([
                'nama' => $item,
                'slug' => Str::slug($item),
            ]);
        }
    }
}
