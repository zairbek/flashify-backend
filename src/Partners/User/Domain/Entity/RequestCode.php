<?php

namespace MarketPlace\Partners\User\Domain\Entity;

use DateInterval;
use DateTime;
use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Partners\User\Domain\Events\SendConfirmationCodeEvent;
use MarketPlace\Partners\User\Infrastructure\Exception\RequestCodeThrottlingException;

class RequestCode implements AggregateRoot
{
    use EventTrait;

    private Uuid $uuid;
    private Uuid $userUuid;
    private Email|Phone $recipient;
    private ConfirmationCode $code;
    private SendAt $sendAt;

    public function __construct(
        Uuid $uuid,
        Uuid $userUuid,
        Email|Phone $recipient,
        ConfirmationCode $code,
    )
    {
        $this->uuid = $uuid;
        $this->userUuid = $userUuid;
        $this->recipient = $recipient;
        $this->code = $code;
        $this->sendAt = SendAt::now();
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getUserUuid(): Uuid
    {
        return $this->userUuid;
    }

    public function getRecipient(): Phone|Email
    {
        return $this->recipient;
    }

    public function getCode(): ConfirmationCode
    {
        return $this->code;
    }

    public function getSendAt(): SendAt
    {
        return $this->sendAt;
    }

    /**
     * @throws RequestCodeThrottlingException
     */
    public function sendSmsConfirmationCode()
    {
        if (
            $this->getSendAt()?->getDateTime()->add(new DateInterval('PT1M'))->getTimestamp()
            > (new DateTime())->getTimestamp()
        ) {
            throw new RequestCodeThrottlingException();
        }

        $this->setSendAt(SendAt::now());
        $this->setCode(ConfirmationCode::generate());

        $this->recordEvent(new SendConfirmationCodeEvent($this));
    }

    public function setSendAt(SendAt $sendAt): void
    {
        $this->sendAt = $sendAt;
    }

    public function setCode(ConfirmationCode $code): void
    {
        $this->code = $code;
    }

    public function setRecipient(Phone|Email $recipient): void
    {
        $this->recipient = $recipient;
    }

    public function isConfirmationCodeCorrect(Email|Phone $recipient, ConfirmationCode $confirmationCode): bool
    {
        if (
            $recipient instanceof Email
            && $this->getRecipient() instanceof Email
        ) {
            return $recipient->getEmail() === $this->getRecipient()->getEmail()
                && $this->getCode()->getCode() === $confirmationCode->getCode();
        }

        return $this->getRecipient()->isEqualTo($recipient)
            && $this->getCode()->getCode() === $confirmationCode->getCode();
    }
}
