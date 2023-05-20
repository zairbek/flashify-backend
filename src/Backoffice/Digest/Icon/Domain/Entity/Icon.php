<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Icon\Domain\Entity;

use MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject\IconFile;
use MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject\IconName;
use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\Uuid;

class Icon implements AggregateRoot
{
    use EventTrait;

    private Uuid $uuid;
    private IconName $name;
    private IconFile $file;

    /**
     * @return Uuid
     */
    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    /**
     * @return IconName
     */
    public function getName(): IconName
    {
        return $this->name;
    }

    /**
     * @return IconFile
     */
    public function getFile(): IconFile
    {
        return $this->file;
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->getUuid()->getId(),
            'name' => $this->getName()->getName(),
            'file' => $this->getFile()->getFile(),
        ];
    }
}
