<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

class AccessToken
{
    private string $accessToken;

    public function __construct(string $accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string
     */
    public function getAccessToken(): string
    {
        return $this->accessToken;
    }
}
