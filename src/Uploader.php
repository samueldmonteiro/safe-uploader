<?php

namespace Samueldmonteiro\SafeUploader;

use Exception;
use Cocur\Slugify\Slugify;


abstract class Uploader
{
    private Slugify $slugfy;

    protected string $baseDir;

    public static array $allowedMimeTypes = [
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        "application/msword",
        "application/pdf",
        "application/vnd.ms-excel",
        "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        "application/vnd.ms-powerpoint",
        "application/vnd.openxmlformats-officedocument.presentationml.presentation",
        "text/plain",
        "application/rtf",
        "application/zip",
        "application/x-7z-compressed",
        "application/x-rar-compressed",
        "application/gzip",
        "audio/mpeg",
        "audio/x-wav",
        "audio/ogg",
        "video/mp4",
        "video/x-matroska",
        "video/x-msvideo",
        "video/quicktime",
        "application/json",
        "application/javascript",
        "text/csv",
        "application/x-shockwave-flash",
        "application/x-tar",

        "image/jpeg",
        "image/png",
        "image/gif",
        "image/bmp",
        "image/webp",
        "image/tiff",
        "image/x-icon",
        "image/svg+xml"
    ];

    public static array $allowedExtensions = [

        "doc",
        "docx",
        "pdf",
        "xls",
        "xlsx",
        "ppt",
        "pptx",
        "txt",
        "rtf",
        "zip",
        "7z",
        "rar",
        "gz",
        "mp3",
        "wav",
        "ogg",
        "mp4",
        "mkv",
        "avi",
        "mov",
        "json",
        "js",
        "csv",
        "swf",
        "tar",

        "jpg",
        "jpeg",
        "png",
        "gif",
        "bmp",
        "webp",
        "tiff",
        "ico",
        "svg"
    ];

    public function __construct(string $baseDir, bool $dirWithDate = false)
    {
        $this->slugfy = new Slugify();
        $this->baseDir = $baseDir;
        $this->handleDirectory($baseDir, $dirWithDate);
    }

    private function handleDirectory(string $baseDir, bool $dirWithDate, $mode = 0755): void
    {
        if ($dirWithDate) {
            $baseDir .=  "/" . date("Y/m");
        }

        if (!is_dir($baseDir)) {
            mkdir($baseDir, $mode, true);
        }
    }

    protected function validateDefault($fileData, $maxSizeMB): bool
    {
        $maxSizeInBytes = $maxSizeMB * 1024 * 1024;

        if ($fileData['size'] > $maxSizeInBytes) {
            throw new Exception("O tamanho do arquivo excede o permitido");
        }

        if ($fileData["error"] || empty($fileData['type']) || empty($fileData['tmp_name'])) {
            throw new Exception("Erro ao ler o arquivo enviado");
        }

        if (!$this->isAllowedType($fileData['type'])) {
            throw new Exception("Tipo de arquivo não permitido");
        }

        if (!$this->isAllowedExtension($this->getExtension($fileData['name']))) {
            throw new Exception("Extensão de arquivo não permitida");
        }

        return true;
    }

    protected function isAllowedType(string $type): bool
    {
        return in_array($type, static::$allowedMimeTypes);
    }

    protected function isAllowedExtension(string $extension): bool
    {
        return in_array($extension, static::$allowedExtensions);
    }

    public function addNewAllowedFile(string $mimetype, string $extension): self
    {
        self::$allowedMimeTypes[] = $mimetype;
        self::$allowedExtensions[] = $extension;
        return $this;
    }

    protected function newFileName(string $fileName): string
    {
        $withoutExtension = pathinfo($fileName, PATHINFO_FILENAME);

        $newFileName = $this->slugfy->slugify($withoutExtension) . '.' . $this->getExtension($fileName);

        return $newFileName;
    }

    protected function getFilePath(string $fileName, bool $pathForDatabase = false): string
    {

        $filePath = $this->baseDir . "/" . $fileName;

        if (!$pathForDatabase) {
            return $filePath;
        }
        return trim(preg_replace(['/\/+/', '/\.\.\//'], ['/', ''], $filePath), '/');
    }

    protected function getExtension(string $fileName): string
    {
        return pathinfo($fileName, PATHINFO_EXTENSION);
    }
}

// 212