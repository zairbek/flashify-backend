<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Domain\Entity;

use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Login;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Market\User\Domain\ValueObject\UserId;

class UserPhoneNumber implements AggregateRoot
{
    use EventTrait;

    private Uuid $uuid;
    private Phone $phone;
    private CreatedAt $createdAt;
    private ?UserId $userId;
    private ?ConfirmationCode $confirmationCode;
    private ?SendAt $sendAt;

    public function __construct(
        Uuid              $uuid,
        Phone             $phone,
        CreatedAt         $createdAt,
        ?UserId           $userId = null,
        ?ConfirmationCode $confirmationCode = null,
        ?SendAt           $sendAt = null,
    )
    {
        $this->uuid = $uuid;
        $this->phone = $phone;
        $this->createdAt = $createdAt;
        $this->userId = $userId;
        $this->confirmationCode = $confirmationCode;
        $this->sendAt = $sendAt;
    }

    /**
     * @return Uuid
     */
    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    /**
     * @return Phone
     */
    public function getPhone(): Phone
    {
        return $this->phone;
    }

    /**
     * @return CreatedAt
     */
    public function getCreatedAt(): CreatedAt
    {
        return $this->createdAt;
    }

    /**
     * @return UserId|null
     */
    public function getUserId(): ?UserId
    {
        return $this->userId;
    }

    /**
     * @return ConfirmationCode|null
     */
    public function getConfirmationCode(): ?ConfirmationCode
    {
        return $this->confirmationCode;
    }

    /**
     * @return SendAt|null
     */
    public function getSendAt(): ?SendAt
    {
        return $this->sendAt;
    }

    public function setUserId(UserId $userId): void
    {
        $this->userId = $userId;
    }
}
