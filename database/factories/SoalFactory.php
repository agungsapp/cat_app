<?php

namespace Database\Factories;

use App\Models\JenisUjian;
use App\Models\Soal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Soal>
 */
class SoalFactory extends Factory
{
    protected $model = Soal::class;

    public function definition()
    {
        return [
            'jenis_id' => JenisUjian::inRandomOrder()->first()->id ?? JenisUjian::factory(),
            'pertanyaan_text' => $this->faker->paragraph(2),
            'media_type' => 'none',
            'media_path' => null,
            // 'skor' => $this->faker->numberBetween(1, 5),
        ];
    }
}
