<?php

namespace App\Services;

use App\Models\GalleryImage;
use Illuminate\Http\UploadedFile;

/**
 * Handles multi-file gallery uploads (images/videos/ZIP archives), shared by
 * the admin Gallery page and an Event's admin Gallery tab. Mirrors the
 * former API's uploadGalleryImage() — including ZIP extraction and image
 * compression — since both admin surfaces accept the same file types.
 */
class GalleryMediaUploader
{
    private const ALLOWED_MEDIA_EXTS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'mov', 'avi', 'webm', 'mkv'];

    /**
     * @param  UploadedFile[]  $files
     * @return GalleryImage[]
     */
    public function upload(array $files, ?int $eventId = null, ?string $caption = null): array
    {
        $created = [];

        foreach ($files as $file) {
            $extension = strtolower($file->getClientOriginalExtension());

            if ($extension === 'zip') {
                $created = array_merge($created, $this->extractZip($file, $eventId, $caption));
            } else {
                $created[] = $this->storeSingleFile($file, $extension, $eventId, $caption);
            }
        }

        return $created;
    }

    private function extractZip(UploadedFile $file, ?int $eventId, ?string $caption): array
    {
        if (! class_exists(\ZipArchive::class)) {
            return [];
        }

        $created = [];
        $zip = new \ZipArchive;

        if ($zip->open($file->getRealPath()) !== true) {
            return [];
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if (str_contains($filename, '__MACOSX') || str_starts_with(basename($filename), '.') || str_ends_with($filename, '/')) {
                continue;
            }

            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (! in_array($ext, self::ALLOWED_MEDIA_EXTS, true)) {
                continue;
            }

            $stream = $zip->getStream($filename);
            if (! $stream) {
                continue;
            }

            $newFileName = time() . '_' . uniqid() . '.' . $ext;
            $destinationDir = public_path('storage/gallery');
            if (! file_exists($destinationDir)) {
                mkdir($destinationDir, 0755, true);
            }

            $destPath = $destinationDir . '/' . $newFileName;
            $tempPath = sys_get_temp_dir() . '/' . uniqid('zip_img_') . '.' . $ext;

            file_put_contents($tempPath, stream_get_contents($stream));
            fclose($stream);

            $this->compressAndSaveImage($tempPath, $destPath, $ext);
            @unlink($tempPath);

            $created[] = GalleryImage::create([
                'event_id' => $eventId,
                'image_path' => '/storage/gallery/' . $newFileName,
                'caption' => $caption,
            ]);
        }

        $zip->close();

        return $created;
    }

    private function storeSingleFile(UploadedFile $file, string $extension, ?int $eventId, ?string $caption): GalleryImage
    {
        $newFileName = time() . '_' . uniqid() . '.' . $extension;
        $destinationDir = public_path('storage/gallery');
        if (! file_exists($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $destPath = $destinationDir . '/' . $newFileName;
        $this->compressAndSaveImage($file->getRealPath(), $destPath, $extension);

        return GalleryImage::create([
            'event_id' => $eventId,
            'image_path' => '/storage/gallery/' . $newFileName,
            'caption' => $caption,
        ]);
    }

    private function compressAndSaveImage(string $sourcePath, string $destPath, string $ext): void
    {
        $ext = strtolower($ext);
        if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp'], true) || ! function_exists('imagecreatefromstring')) {
            copy($sourcePath, $destPath);

            return;
        }

        try {
            $content = file_get_contents($sourcePath);
            $srcImage = $content ? @imagecreatefromstring($content) : false;

            if (! $srcImage) {
                copy($sourcePath, $destPath);

                return;
            }

            $width = imagesx($srcImage);
            $height = imagesy($srcImage);
            $maxDimension = 1920;

            if ($width > $maxDimension || $height > $maxDimension) {
                if ($width >= $height) {
                    $newWidth = $maxDimension;
                    $newHeight = (int) round(($height / $width) * $maxDimension);
                } else {
                    $newHeight = $maxDimension;
                    $newWidth = (int) round(($width / $height) * $maxDimension);
                }

                $dstImage = imagecreatetruecolor($newWidth, $newHeight);

                if ($ext === 'png' || $ext === 'webp') {
                    imagealphablending($dstImage, false);
                    imagesavealpha($dstImage, true);
                }

                imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($srcImage);
                $srcImage = $dstImage;
            }

            match ($ext) {
                'png' => imagepng($srcImage, $destPath, 7),
                'webp' => imagewebp($srcImage, $destPath, 75),
                default => imagejpeg($srcImage, $destPath, 75),
            };

            imagedestroy($srcImage);
        } catch (\Throwable) {
            copy($sourcePath, $destPath);
        }
    }
}
