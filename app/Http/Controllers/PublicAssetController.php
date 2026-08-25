<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PublicAssetController extends Controller
{
    public function __invoke(string $path): BinaryFileResponse
    {
        $segments = explode('/', str_replace('\\', '/', $path));

        abort_if(
            collect($segments)->contains(fn (string $segment) => $segment === '' || str_starts_with($segment, '.')),
            404,
        );

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $allowedExtensions = config('filesystems.disks.assets.allowed_extensions', []);

        abort_unless(in_array($extension, $allowedExtensions, true), 404);

        $disk = Storage::disk('assets');

        abort_unless($disk->exists($path), 404);

        return response()->file($disk->path($path), [
            'Cache-Control' => 'public, max-age=3600',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
