<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

class Url
{
    private string $url;

    public function __construct(string $url)
    {
        $this->url = $url;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }
}
