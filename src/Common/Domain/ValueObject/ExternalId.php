<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

class ExternalId
{
    private string $externalId;

    public function __construct(string $externalId)
    {
        $this->externalId = $externalId;
    }

    /**
     * @return string
     */
    public function getExternalId(): string
    {
        return $this->externalId;
    }
}
