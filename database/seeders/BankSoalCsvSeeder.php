<?php

namespace Database\Seeders;

use App\Models\JenisUjian;
use App\Models\Soal;
use App\Models\SoalOpsi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class BankSoalCsvSeeder extends Seeder
{
    public function run(): void
    {
        // separator ; (Excel Indonesia)
        $this->seedSoalFromCsv('TWK', 'seeders/data/soal-twk2.csv', ',');
        $this->seedSoalFromCsv('TKP', 'seeders/data/soal-tkp.csv', ';');

    }

    /**
     * Seeder reusable untuk semua jenis ujian (TWK / TIU / TKP)
     */
    private function seedSoalFromCsv(
        string $kodeJenis,
        string $csvRelativePath,
        string $separator = ';'
    ): void {
        $filePath = database_path($csvRelativePath);

        if (!file_exists($filePath)) {
            $this->command?->error("File CSV tidak ditemukan: {$csvRelativePath}");
            return;
        }

        $jenis = JenisUjian::where('kode', $kodeJenis)->first();

        if (!$jenis) {
            $this->command?->error("Jenis ujian '{$kodeJenis}' tidak ditemukan.");
            return;
        }

        if (($handle = fopen($filePath, 'r')) === false) {
            $this->command?->error("Gagal membuka file CSV: {$csvRelativePath}");
            return;
        }

        // Skip header
        fgetcsv($handle, 0, $separator);

        $success   = 0;
        $rowNumber = 1;

        while (($row = fgetcsv($handle, 0, $separator)) !== false) {
            $rowNumber++;

            // =========================
            // TWK / TIU
            // =========================
            if ($jenis->tipe_penilaian === 'benar_salah') {

                if (count($row) < 7) {
                    Log::warning("Baris TWK/TIU tidak lengkap ({$csvRelativePath} baris {$rowNumber})");
                    continue;
                }

                [
                    $pertanyaan,
                    $a,
                    $b,
                    $c,
                    $d,
                    $e,
                    $jawaban
                ] = $row;

                $jawaban = strtoupper(trim($jawaban));

                if (!in_array($jawaban, ['A', 'B', 'C', 'D', 'E'])) {
                    Log::warning("Jawaban tidak valid ({$csvRelativePath} baris {$rowNumber})");
                    continue;
                }

                $soal = Soal::create([
                    'jenis_id'        => $jenis->id,
                    'pertanyaan_text' => trim($pertanyaan),
                    'media_type'      => 'none',
                    'media_path'      => null,
                ]);

                $opsiList = [
                    ['A', $a],
                    ['B', $b],
                    ['C', $c],
                    ['D', $d],
                    ['E', $e],
                ];

                foreach ($opsiList as [$label, $teks]) {
                    if (empty(trim($teks))) continue;

                    SoalOpsi::create([
                        'soal_id'    => $soal->id,
                        'label'      => $label,
                        'teks'       => trim($teks),
                        'is_correct' => $jawaban === $label,
                        'skor'       => null,
                    ]);
                }

                $success++;
            }

            // =========================
            // TKP
            // =========================
            elseif ($jenis->tipe_penilaian === 'bobot_opsi') {

                if (count($row) < 11) {
                    Log::warning("Baris TKP tidak lengkap ({$csvRelativePath} baris {$rowNumber})");
                    continue;
                }

                [
                    $pertanyaan,
                    $a,
                    $skorA,
                    $b,
                    $skorB,
                    $c,
                    $skorC,
                    $d,
                    $skorD,
                    $e,
                    $skorE
                ] = $row;

                $soal = Soal::create([
                    'jenis_id'        => $jenis->id,
                    'pertanyaan_text' => trim($pertanyaan),
                    'media_type'      => 'none',
                    'media_path'      => null,
                ]);

                $opsiList = [
                    ['A', $a, $skorA],
                    ['B', $b, $skorB],
                    ['C', $c, $skorC],
                    ['D', $d, $skorD],
                    ['E', $e, $skorE],
                ];

                foreach ($opsiList as [$label, $teks, $skor]) {
                    if (empty(trim($teks))) continue;

                    $skor = (int) $skor;
                    if ($skor < 1 || $skor > 5) {
                        Log::warning("Skor TKP tidak valid ({$csvRelativePath} baris {$rowNumber})");
                        continue;
                    }

                    SoalOpsi::create([
                        'soal_id'    => $soal->id,
                        'label'      => $label,
                        'teks'       => trim($teks),
                        'is_correct' => false,
                        'skor'       => $skor,
                    ]);
                }

                $success++;
            }
        }

        fclose($handle);

        $this->command?->info("✔ {$success} soal {$kodeJenis} berhasil di-import dari {$csvRelativePath}");
    }
}
