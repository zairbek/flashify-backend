<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

use Webmozart\Assert\Assert;

class Email
{
    private string $email;

    public function __construct(string $email)
    {
        Assert::email($email);

        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
