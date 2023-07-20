<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Digest\Location\Domain\Entity;

use MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject\IconFile;
use MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject\IconName;
use MarketPlace\Backoffice\Digest\Location\Domain\ValueObject\LocationName;
use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\ExternalId;
use MarketPlace\Common\Domain\ValueObject\Id;
use MarketPlace\Common\Domain\ValueObject\Uuid;

class Location implements AggregateRoot
{
    use EventTrait;

    private ?Id $id;
    private ?Id $parentId;
    private ?ExternalId $externalId;
    private LocationName $name;

    public function __construct(
        ?Id          $id,
        ?Id          $parentId,
        ?ExternalId  $externalId,
        LocationName $name,
    )
    {
        $this->id = $id;
        $this->parentId = $parentId;
        $this->externalId = $externalId;
        $this->name = $name;
    }

    public function getId(): ?Id
    {
        return $this->id;
    }

    public function getParentId(): ?Id
    {
        return $this->parentId;
    }

    public function getExternalId(): ?ExternalId
    {
        return $this->externalId;
    }

    public function getName(): LocationName
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->getId()?->getId(),
            'parentId' => $this->getParentId()?->getId(),
            'externalId' => $this->getExternalId()?->getExternalId(),
            'name' => $this->getName()->getName(),
            'translates' => $this->getName()->translatesToArray(),
        ];
    }
}
