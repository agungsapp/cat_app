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
        // $jenis = [
        //     "TWK (Tes Wawasan Kebangsaan)",
        //     "TIU (Tes Intelegensi Umum)",
        //     "TKP (Tes Karakteristik Pribadi)",
        //     "SKD (Soal Kompetensi Dasar)",
        //     "SKB (Soal Kompetensi Bidang)",
        // ];

        $jenis = [
            [
                'nama' => 'Tes Wawasan Kebangsaan',
                'tipe_penilaian' => 'benar_salah',
                'bobot_per_soal' => 5,
            ],
            [
                'nama' => 'Tes Intelegensi Umum',
                'tipe_penilaian' => 'benar_salah',
                'bobot_per_soal' => 5,
            ],
            [
                'nama' => 'Tes Karakteristik Pribadi',
                'tipe_penilaian' => 'bobot_opsi',
            ],
            [
                'nama' => 'Soal Kompetensi Dasar',
                'tipe_penilaian' => 'benar_salah',
                'bobot_per_soal' => 5,
            ],
            [
                'nama' => 'Soal Kompetensi Bidang',
                'tipe_penilaian' => 'benar_salah',
                'bobot_per_soal' => 5,
            ],
        ];

        $tipes = [
            [
                'nama' => 'Simulasi',
                'slug' => 'simulasi',
                'max_attempt' => 3,
                'mode' => 'fixed_rule',
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
                'mode' => 'random_all',
            ],
        ];

        foreach ($jenis as $item) {
            \App\Models\JenisUjian::create([
                'kode' => \App\Models\JenisUjian::generateInitials($item['nama']),
                'nama' => $item['nama'],
                'tipe_penilaian' => $item['tipe_penilaian'],
                'bobot_per_soal' => $item['bobot_per_soal'] ?? null,
            ]);
        }
        foreach ($tipes as $tipe) {
            \App\Models\TipeUjian::create($tipe);
        }
    }
}
