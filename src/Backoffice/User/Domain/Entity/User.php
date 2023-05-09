<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\User\Domain\Entity;

use MarketPlace\Backoffice\User\Domain\ValueObject\UserName;
use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Password;
use MarketPlace\Common\Domain\ValueObject\Sex;
use MarketPlace\Common\Domain\ValueObject\UserStatus;
use MarketPlace\Common\Domain\ValueObject\Uuid;

class User implements AggregateRoot
{
    use EventTrait;

    private Uuid $uuid;
    private Email $email;
    private ?UserName $userName;
    private ?Sex $sex;
    private ?Password $password;
    private UserStatus $status;

    public function __construct(Uuid $uuid, Email $email, Password $password)
    {
        $this->uuid = $uuid;
        $this->email = $email;
        $this->password = $password;
    }

    /**
     * @return Uuid
     */
    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    /**
     * @return UserName|null
     */
    public function getUserName(): ?UserName
    {
        return $this->userName;
    }

    /**
     * @return Sex|null
     */
    public function getSex(): ?Sex
    {
        return $this->sex;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
    }

    /**
     * @return UserStatus
     */
    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    /**
     * @return Password|null
     */
    public function getPassword(): ?Password
    {
        return $this->password;
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->getUuid()->getId(),
            'email' => $this->getEmail()->getEmail(),
            'name' => [
                'firstName' => $this->getUserName()?->getFirstName(),
                'lastName' => $this->getUserName()?->getLastName(),
            ],
            'sex' => $this->getSex()?->getSex(),
            'status' => $this->getStatus()->getStatus()
        ];
    }
}
