<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

use Illuminate\Http\UploadedFile;

class File
{
    private string $originalName;
    private string $filename;
    private string $mimeType;
    private string $dirPath;
    private string $filePath;

    public function __construct(
        string $originalName,
        string $filename,
        string $mimeType,
        string $dirPath,
        string $filePath,
    )
    {
        $this->originalName = $originalName;
        $this->filename = $filename;
        $this->mimeType = $mimeType;
        $this->dirPath = $dirPath;
        $this->filePath = $filePath;
    }

    public static function fromUploadedFile(UploadedFile $file): static
    {
        return new static(
            originalName: $file->getClientOriginalName(),
            filename: $file->getFilename(),
            mimeType: $file->getMimeType(),
            dirPath: $file->getPath(),
            filePath: $file->getPathname(),
        );
    }

    /**
     * @return string
     */
    public function getOriginalName(): string
    {
        return $this->originalName;
    }

    /**
     * @return string
     */
    public function getFilename(): string
    {
        return $this->filename;
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    /**
     * @return string
     */
    public function getDirPath(): string
    {
        return $this->dirPath;
    }

    /**
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->filePath;
    }
}
