<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('images:fix-extensions {--default=jpg}', function () {
    $disk = Storage::disk('public');
    $default = $this->option('default') ?: 'jpg';
    $fixed = 0;

    $files = $disk->allFiles('businesses');
    foreach ($files as $path) {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        if (!$ext) {
            // Try to detect mime type
            try {
                $mime = $disk->mimeType($path);
            } catch (\Throwable $e) {
                $mime = null;
            }

            $guessed = $default;
            if ($mime && str_contains($mime, '/')) {
                [$type, $sub] = explode('/', $mime, 2);
                $guessed = strtolower($sub ?: $default);
            }
            if ($guessed === 'jpeg') {
                $guessed = 'jpg';
            }

            $newPath = $path . '.' . $guessed;
            if ($disk->move($path, $newPath)) {
                Image::query()->where('path', $path)->update([
                    'path' => $newPath,
                    'filename' => basename($newPath),
                ]);
                $this->info("Renamed: {$path} -> {$newPath}");
                $fixed++;
            } else {
                $this->error("Failed to move: {$path}");
            }
        }
    }

    $this->info("Done. Fixed {$fixed} files.");
})->purpose('Append proper extensions to stored images and update DB paths');
