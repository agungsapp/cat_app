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
        Soal::factory(100)->create()->each(function ($soal) {
            $jenis = $soal->jenis;

            // === TWK / TIU ===
            if ($jenis->tipe_penilaian === 'benar_salah') {
                $labels = ['A', 'B', 'C', 'D', 'E'];
                $correctIndex = rand(0, 4);

                foreach ($labels as $i => $label) {
                    SoalOpsi::create([
                        'soal_id' => $soal->id,
                        'label' => $label,
                        'teks' => fake()->sentence(),
                        'is_correct' => $i === $correctIndex,
                        'skor' => null,
                    ]);
                }
            }

            // === TKP ===
            if ($jenis->tipe_penilaian === 'bobot_opsi') {
                $labels = ['A', 'B', 'C', 'D', 'E'];
                $scores = [1, 2, 3, 4, 5];
                shuffle($scores);

                foreach ($labels as $i => $label) {
                    SoalOpsi::create([
                        'soal_id' => $soal->id,
                        'label' => $label,
                        'teks' => fake()->sentence(),
                        'is_correct' => false,
                        'skor' => $scores[$i],
                    ]);
                }
            }
        });
    }
}
