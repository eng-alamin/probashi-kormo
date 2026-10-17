<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * One place for every admin image upload (gallery, news cover, story photo).
 *
 * - Files go to the "public" disk, in one folder per module, with a random file name.
 * - Only JPG / PNG / WebP are accepted. SVG is left out on purpose: it can carry scripts.
 * - The database keeps only the path (for example "gallery/abc123.jpg").
 */
final class ImageStore
{
    public const DISK = 'public';

    public const MAX_KB = 4096;

    /** Folders this class is allowed to write to and delete from. */
    private const FOLDERS = ['gallery', 'news', 'stories'];

    /**
     * Validation rules shared by every image field.
     *
     * @return list<string>
     */
    public static function rules(): array
    {
        return ['image', 'mimes:jpg,jpeg,png,webp', 'max:'.self::MAX_KB];
    }

    /**
     * Messages for an image field. Pass 'photo' or 'photos.*'.
     *
     * @return array<string, string>
     */
    public static function messages(string $field): array
    {
        return [
            "{$field}.image" => 'Choose an image file (JPG, PNG or WebP).',
            "{$field}.mimes" => 'Only JPG, PNG or WebP images are allowed.',
            "{$field}.max" => 'Each image must be '.(self::MAX_KB / 1024).' MB or smaller.',
            "{$field}.uploaded" => 'The upload failed. The image may be larger than the server allows.',
        ];
    }

    public static function put(UploadedFile $file, string $folder): string
    {
        abort_unless(in_array($folder, self::FOLDERS, true), 500, 'Unknown image folder.');

        return (string) $file->store($folder, self::DISK);
    }

    /**
     * Removes a stored image. Silently ignores blank, odd or out-of-folder paths,
     * so a bad value in the database can never delete something else.
     */
    public static function delete(?string $path): void
    {
        $path = trim((string) $path);

        if ($path === '' || str_contains($path, '..') || ! str_contains($path, '/')) {
            return;
        }

        if (! in_array(explode('/', $path)[0], self::FOLDERS, true)) {
            return;
        }

        Storage::disk(self::DISK)->delete($path);
    }

    public static function url(?string $path): ?string
    {
        $path = trim((string) $path);

        return $path === '' ? null : asset('storage/'.$path);
    }

    /**
     * Temporary preview of a just-selected file (before Save). Null when the browser cannot preview it.
     */
    public static function previewUrl(mixed $file): ?string
    {
        if (! is_object($file) || ! method_exists($file, 'isPreviewable') || ! $file->isPreviewable()) {
            return null;
        }

        try {
            return $file->temporaryUrl();
        } catch (\Throwable) {
            return null;
        }
    }
}
