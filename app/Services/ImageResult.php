<?php

namespace App\Services;

readonly class ImageResult
{
    public function __construct(
        public string $path,
        public ?string $thumbnailPath,
        public string $hash,
        public string $mimeType,
        public int $size,
    ) {}
}
