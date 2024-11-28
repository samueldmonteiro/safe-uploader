<?php

namespace Samueldmonteiro\SafeUploader;

use Exception;
use Intervention\Image\Exception\ImageException;
use Intervention\Image\ImageManager;

class Image extends Uploader
{
    private ImageManager $engine;

    /**
     * resource file
     */
    protected $file;

    public function __construct(string $baseDir, bool $usageMonthYearPath = false)
    {
        parent::__construct($baseDir, $usageMonthYearPath);
        $this->engine = new ImageManager();
    }

    public function upload(array $data, string $fileName, int $width = 2000, int $quality = 90, int $maxSize = 8388608): ?string
    {
        //gereralvaldate

        $this->ext = $this->getExtension($data['name']);
        $this->name = $this->createName($fileName);
        $this->baseDir = $this->fullPath();

        if ($this->isGif($data)) {
            move_uploaded_file($data['tmp_name'], $this->baseDir);
            return $this->baseDir;
        }

        if (!$this->generateImage($data['tmp_name'], $width)) {
            throw new Exception("error in generate image");
        }

        if (!$this->save($quality)) {
            throw new Exception("error in save");
        }
        return $this->baseDir;
    }

    private function isGif(array $data): bool
    {
        if ($data['type'] == "image/gif") {
            return true;
        } else {
            return false;
        }
    }

    private function generateImage(string $fileLocal, int $width): bool
    {
        try {

            $this->file = $this->engine->make($fileLocal)->resize($width, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            return true;
        } catch (ImageException | Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function save(int $quality): bool
    {
        try {
            $this->file->save($this->baseDir, $quality);
            return true;
        } catch (ImageException | Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}

//138