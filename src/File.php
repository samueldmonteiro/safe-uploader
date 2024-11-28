<?php

namespace Samueldmonteiro\SafeUploader;

use Exception;

class File extends Uploader
{
    public function upload(array $fileData, $maxSizeMB = 5): array|string
    {
        $this->validateDefault($fileData, $maxSizeMB);

        $fileName = $this->newFileName($fileData['name']);
        $filePath = $this->getFilePath($fileName);


        if (!$this->save($fileData['tmp_name'], $filePath)) {
            throw new Exception("erro ao salvar arquivo");
        }

        return [
            'filePath' => $filePath,
            'databasePath' => $this->getFilePath($fileName, true)
        ];
    }

    public function multipleUpload(array $filesData) {}

    private function save(string $tmpLocal, string $saveLocal): bool
    {
        return move_uploaded_file($tmpLocal, $saveLocal);
    }
}

//68