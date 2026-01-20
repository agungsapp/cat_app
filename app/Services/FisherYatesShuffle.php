<?php

namespace App\Services;

use Illuminate\Support\Collection;

class FisherYatesShuffle
{
    /**
     * Shuffle collection menggunakan algoritma Fisher-Yates
     * 
     * @param Collection $collection
     * @param int|null $seed (Opsional: untuk reproducible shuffle)
     * @return Collection
     */
    public function shuffle(Collection $collection, ?int $seed = null): Collection
    {
        // Clone collection agar tidak mengubah original
        $items = $collection->values()->all();
        $n = count($items);

        // Jika ada seed, set untuk reproducible random
        if ($seed !== null) {
            mt_srand($seed);
        }

        // Fisher-Yates Shuffle Algorithm
        for ($i = $n - 1; $i > 0; $i--) {
            // Generate random index antara 0 dan i
            $j = mt_rand(0, $i);

            // Swap elemen ke-i dengan elemen ke-j
            $temp = $items[$i];
            $items[$i] = $items[$j];
            $items[$j] = $temp;
        }

        // Reset random seed (penting!)
        if ($seed !== null) {
            mt_srand();
        }

        // ✅ Return sebagai collection (preserve model objects)
        return collect($items);
    }

    /**
     * Shuffle dengan group (untuk shuffle per jenis soal)
     * 
     * @param Collection $collection
     * @param string $groupByKey (e.g., 'jenis_id')
     * @param int|null $seed
     * @return Collection
     */
    public function shuffleGrouped(Collection $collection, string $groupByKey, ?int $seed = null): Collection
    {
        // Group by key (e.g., jenis_id)
        $grouped = $collection->groupBy($groupByKey);

        $result = collect();

        // Shuffle setiap group dan gabungkan
        foreach ($grouped as $group) {
            $shuffled = $this->shuffle($group, $seed);
            $result = $result->concat($shuffled);  // ✅ Concat (bukan flatten)
        }

        // ✅ Values untuk reset index ke 0,1,2... tapi tetap preserve object
        return $result->values();
    }

    /**
     * Generate seed unik berdasarkan user_id + sesi_ujian_id
     * (Untuk reproducible shuffle per user per sesi)
     * 
     * @param int $userId
     * @param int $sesiUjianId
     * @return int
     */
    public function generateSeed(int $userId, int $sesiUjianId): int
    {
        // Kombinasi user_id + sesi_ujian_id
        // Hasilnya unique per user per sesi
        return crc32("{$userId}-{$sesiUjianId}");
    }
}
