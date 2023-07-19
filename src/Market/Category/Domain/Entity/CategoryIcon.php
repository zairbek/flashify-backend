<?php

declare(strict_types=1);

namespace MarketPlace\Market\Category\Domain\Entity;

use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\Url;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Market\Category\Domain\ValueObject\Name;

class CategoryIcon implements AggregateRoot
{
    use EventTrait;

    private Uuid $uuid;
    private Name $name;
    private Url $url;

    /**
     * @return Uuid
     */
    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    /**
     * @return Name
     */
    public function getName(): Name
    {
        return $this->name;
    }

    /**
     * @return Url
     */
    public function getUrl(): Url
    {
        return $this->url;
    }
}
