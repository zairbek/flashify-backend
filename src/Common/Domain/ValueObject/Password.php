<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

use Webmozart\Assert\Assert;

class Password
{
    private string $password;

    public function __construct(string $password)
    {
        Assert::stringNotEmpty($password);

        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
