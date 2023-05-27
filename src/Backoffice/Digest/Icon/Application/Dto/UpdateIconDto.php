<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Icon\Application\Dto;

use MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject\IconFile;

class UpdateIconDto
{
    public function __construct(
        public string $uuid,
        public string $name,
        public ?IconFile $file,
    )
    {
    }
}
