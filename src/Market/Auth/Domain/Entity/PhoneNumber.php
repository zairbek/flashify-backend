<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Domain\Entity;

use DateInterval;
use DateTime;
use Exception;
use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Market\Auth\Domain\Events\SendConfirmationCodeForPhoneNumberEvent;
use MarketPlace\Market\Auth\Domain\Exception\SendSmsThrottleException;
use MarketPlace\Market\Auth\Domain\ValueObject\UserId;

class PhoneNumber implements AggregateRoot
{
    use EventTrait;

    private Uuid $uuid;
    private Phone $phone;

    /**
     * @return Phone
     */
    public function getPhone(): Phone
    {
        return $this->phone;
    }
    private CreatedAt $createdAt;
    private ?UserId $userId;
    private ?ConfirmationCode $confirmationCode = null;
    private ?SendAt $sendAt = null;

    /**
     * @throws Exception
     */
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
        $this->confirmationCode = $confirmationCode;
        $this->sendAt = $sendAt;
        $this->userId = $userId;
    }

    /**
     * @return UserId|null
     */
    public function getUserId(): ?UserId
    {
        return $this->userId;
    }

    /**
     * @throws SendSmsThrottleException
     * @throws Exception
     */
    public function sendConfirmationCode(?ConfirmationCode $code = null): void
    {
        $this->confirmationCode = $code ?? ConfirmationCode::generate();

        if (is_null($this->sendAt)) {
            $this->sendAt = SendAt::now();
        } else if (
            $this->sendAt->getDateTime()->add(new DateInterval('PT1M'))->getTimestamp()
            > (new DateTime())->getTimestamp()
        ) {
            throw new SendSmsThrottleException();
        }

        $this->recordEvent(new SendConfirmationCodeForPhoneNumberEvent($this->phone, $this->confirmationCode));
    }

    public function isCodeNotMatch(ConfirmationCode $code): bool
    {
        return !$this->isCodeMatch($code);
    }

    public function isCodeMatch(ConfirmationCode $code): bool
    {
        return $this->confirmationCode?->getCode() === $code->getCode();
    }

    public function clearTempData(): void
    {
        $this->confirmationCode = null;
        $this->sendAt = null;
    }
}
