<?php

namespace App\Http\Controllers;

use App\Models\Konten;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PdfViewController extends Controller
{
    public function show(Konten $konten)
    {
        // $this->authorizeKonten($konten);

        $path = $konten->file_path;
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404, 'File PDF tidak ditemukan.');
        }

        $file = Storage::disk('public')->path($path);

        return response()->file($file, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
            'X-Frame-Options' => 'SAMEORIGIN', // Biar bisa di-iframe
            'Cache-Control' => 'private',
        ]);
    }

    public function download(Konten $konten)
    {
        // Untuk download PDF
        if ($konten->tipe !== 'pdf') {
            abort(404, 'Konten bukan PDF');
        }

        $filePath = trim($konten->file_path, '/');

        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'File PDF tidak ditemukan');
        }

        return Storage::disk('public')->download($filePath, basename($filePath));
    }
}
