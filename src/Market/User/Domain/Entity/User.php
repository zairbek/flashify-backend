<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Domain\Entity;

use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Login;
use MarketPlace\Common\Domain\ValueObject\Password;
use MarketPlace\Common\Domain\ValueObject\Sex;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Market\User\Domain\ValueObject\UserName;

class User implements AggregateRoot
{
    use EventTrait;

    private Uuid $uuid;
    private Login $login;
    private ?UserName $userName;
    private ?Sex $sex;
    private ?Email $email;
    private ?Password $password;

    public function __construct(Uuid $uuid, Login $login)
    {
        $this->uuid = $uuid;
        $this->login = $login;
    }

    public static function createFromPhone(Uuid $uuid, Login $login): self
    {
        return new self($uuid, $login);
    }

    /**
     * @return Uuid
     */
    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    /**
     * @return Login
     */
    public function getLogin(): Login
    {
        return $this->login;
    }
}
