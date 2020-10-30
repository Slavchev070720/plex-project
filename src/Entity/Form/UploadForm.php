<?php

namespace App\Entity\Form;

use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadForm
{
    /** @var UploadedFile|null */
    private $sqlite;

    /**
     * @return UploadedFile|null
     */
    public function getSqlite(): ?UploadedFile
    {
        return $this->sqlite;
    }

    /**
     * @param UploadedFile|null $sqlite
     */
    public function setSqlite(?UploadedFile $sqlite): void
    {
        $this->sqlite = $sqlite;

    }
}