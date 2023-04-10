<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

use Exception;
use Webmozart\Assert\Assert;

class Login
{
    private string $login;

    public function __construct(string $login)
    {
        Assert::stringNotEmpty($login);

        $this->login = $login;
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
    }

    public static function generate(): self
    {
        $permitted_chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $login = 'user_' . substr(str_shuffle($permitted_chars), 0, 16);

        return new self($login);
    }
}
