<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject;

class IconFile
{
    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }
}
