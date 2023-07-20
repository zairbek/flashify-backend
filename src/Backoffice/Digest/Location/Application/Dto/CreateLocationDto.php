<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Location\Application\Dto;

use MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject\IconFile;

class CreateLocationDto
{
    public function __construct(
        public string $name,
        public IconFile $file,
    )
    {
    }
}
