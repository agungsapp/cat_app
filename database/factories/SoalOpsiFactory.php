<?php

namespace Database\Factories;

use App\Models\SoalOpsi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SoalOpsi>
 */
class SoalOpsiFactory extends Factory
{
    protected $model = SoalOpsi::class;

    public function definition()
    {
        return [
            'soal_id' => null,
            'label' => $this->faker->randomElement(['A', 'B', 'C', 'D', 'E']),
            'teks' => $this->faker->sentence(),
            'media_type' => 'none',
            'media_path' => null,
            'is_correct' => false,
            'skor' => null,
        ];
    }
}
