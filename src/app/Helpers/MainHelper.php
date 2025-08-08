<?php

namespace App\Helpers;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MainHelper
{
    public static function branchCode()
    {
        return auth()->user()->branch_code;
    }

    public static function vueGlobalVariables(): string
    {
        return json_encode([
            'csrf' => csrf_token(),
            'app' => config('app.name'),
            'version' => config('app.version'),
            'api' => config('app.url') . '/api',
        ]);
    }

    public static function deleteImage(string $previmg): bool
    {
        $path = env("PROFILE_PATH");
        $disk = env("PROFILE_DISK");
        if (Str::contains($previmg, 'default') === false) {
            return Storage::disk($disk)->delete($path . $previmg);
        }
        return true;
    }

    public static function changeImage(string $previmg, UploadedFile $file, string $dni): ?string
    {
        $path = env("PROFILE_PATH");
        $disk = env("PROFILE_DISK");
        if (self::deleteImage($previmg)) {
            $fileName = "profile_" . $dni . "_" . rand(100, 999) . ".png";
            Storage::disk($disk)->putFileAs($path, $file, $fileName);
            return $fileName;
        }
        return false;
    }

    public static function to_pg_array($set): string
    {
        array_walk($set, function (&$value) {
            $value = is_numeric($value) ? $value : '"' . addslashes($value) . '"';
        });

        return '{' . implode(",", $set) . '}';
    }
}
