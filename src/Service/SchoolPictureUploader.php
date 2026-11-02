<?php

namespace App\Service;

use App\Entity\School;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

// TODO make it more universal
readonly class SchoolPictureUploader
{
    public function __construct(
        private string $targetDirectory,
    ) {}

    public function upload(string $schoolUuid, UploadedFile $file): string
    {
        $fileName = sprintf('%s.png', $schoolUuid);
        $targetPath = $this->targetDirectory . '/' . $fileName;

        $image = $this->createImageFromFile($file);

        imagesavealpha($image, true);

        if (!imagepng($image, $targetPath)) {
            throw new RuntimeException('Failed to save converted image.');
        }

        imagedestroy($image);

        return $fileName;
    }

    private function createImageFromFile(UploadedFile $file): \GdImage
    {
        $mimeType = $file->getMimeType();

        $image = match ($mimeType) {
            'image/jpeg' => imagecreatefromjpeg($file->getPathname()),
            'image/png' => imagecreatefrompng($file->getPathname()),
            'image/webp' => imagecreatefromwebp($file->getPathname()),
            default => throw new RuntimeException('Unsupported image type: ' . $mimeType),
        };

        if ($image === false) {
            throw new RuntimeException('Failed to read uploaded image.');
        }

        return $image;
    }

    public function getPictureUrl(string $schoolUuid): string
    {
        $path = $this->targetDirectory . '/' . $schoolUuid . '.png';
        $version = file_exists($path) ? filemtime($path) : 0;

        return '/storage/images/school/' . $schoolUuid . '.png?v=' . $version;
    }
}
