<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Domain\Entity;

use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\Login;
use MarketPlace\Common\Domain\ValueObject\Password;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Partners\User\Domain\ValueObject\Email;
use MarketPlace\Partners\User\Domain\ValueObject\Phone;
use MarketPlace\Partners\User\Domain\ValueObject\UserName;
use MarketPlace\Partners\User\Domain\ValueObject\UserStatus;

class User implements AggregateRoot
{
    use EventTrait;

    private Uuid $uuid;
    private Login $login;
    private ?Email $email;
    private ?Phone $phone;
    private ?UserName $userName;
    private ?Password $password;
    private UserStatus $status;

    public function __construct(Uuid $uuid, Login $login)
    {
        $this->uuid = $uuid;
        $this->login = $login;
    }

    public static function createFromPhone(Uuid $uuid, Login $login): self
    {
        return new self($uuid, $login);
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getLogin(): Login
    {
        return $this->login;
    }

    public function getAccountName(): ?UserName
    {
        return $this->userName;
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->getUuid()->getId(),
            'login' => $this->getLogin()->getLogin(),
            'email' => $this->getEmail()?->getEmail(),
            'phone' => $this->getPhone()
                ? [
                    'regionIsoCode' => $this->getPhone()->getRegionCode(),
                    'number' => $this->getPhone()->toString()
                ]
                : null,
            'name' => [
                'firstName' => $this->getAccountName()?->getFirstName(),
                'lastName' => $this->getAccountName()?->getLastName(),
            ],
            'status' => $this->getStatus()->getStatus()
        ];
    }

    public function changeUserName(?UserName $userName): void
    {
        $this->userName = $userName;
    }
}
