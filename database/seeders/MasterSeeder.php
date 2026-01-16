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
            'Simulasi' => 3,
            'Latihan' => null,
            'Tryout' => 1,
        ];

        $tipes = [
            [
                'nama' => 'Simulasi',
                'slug' => 'simulasi',
                'max_attempt' => 3,
                'mode' => 'random_all',
            ],
            [
                'nama' => 'Latihan',
                'slug' => 'latihan',
                'max_attempt' => null,
                'mode' => 'random_by_jenis',
            ],
            [
                'nama' => 'Tryout',
                'slug' => 'tryout',
                'max_attempt' => 1,
                'mode' => 'fixed_rule',
            ],
        ];

        foreach ($jenis as $item) {
            \App\Models\JenisUjian::create([
                'nama' => $item,
            ]);
        }
        foreach ($tipes as $tipe) {
            \App\Models\TipeUjian::create($tipe);
        }
    }
}
