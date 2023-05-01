<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Domain\Entity;

use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Domain\ValueObject\VerifiedAt;
use MarketPlace\Market\Auth\Domain\ValueObject\UserId;

class UserEmail implements AggregateRoot
{
    use EventTrait;

    private Uuid $uuid;
    private Email $email;
    private ?ConfirmationCode $confirmationCode;
    private ?SendAt $sendAt;
    private ?VerifiedAt $verifiedAt;
    private UserId $userUuid;

    /**
     * @return Uuid
     */
    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
    {
        return $this->email;
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

    /**
     * @return VerifiedAt|null
     */
    public function getVerifiedAt(): ?VerifiedAt
    {
        return $this->verifiedAt;
    }

    /**
     * @return UserId
     */
    public function getUserUuid(): UserId
    {
        return $this->userUuid;
    }

    public function toArray(): array
    {
        return [
            'uuid' => $this->getUuid()->getId(),
            'email' => $this->getEmail()->getEmail(),
            'confirmationCode' => $this->getConfirmationCode()?->getCode(),
            'sendAt' => $this->getSendAt()?->toIsoFormat(),
            'verifiedAt' => $this->getVerifiedAt()?->toIsoFormat(),
            'userUuid' => $this->getUserUuid()->getUserId()->getId(),
        ];
    }
}
