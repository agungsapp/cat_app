<?php

namespace Database\Seeders;

use App\Models\JenisUjian;
use App\Models\Soal;
use App\Models\SoalOpsi;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSoalSeeder extends Seeder
{
    public function run()
    {
        // Pastikan ada Jenis Ujian
        // JenisUjian::factory(5)->create();

        Soal::factory(3)->create()->each(function ($soal) {
            $opsiCount = rand(4, 6);
            $correctIndex = rand(0, $opsiCount - 1);

            SoalOpsi::factory($opsiCount)->create([
                'soal_id' => $soal->id,
            ])->each(function ($opsi, $key) use ($correctIndex) {
                if ($key === $correctIndex) {
                    $opsi->update(['is_correct' => true]);
                }
            });
        });
    }
}
