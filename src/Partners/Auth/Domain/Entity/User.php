<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Domain\Entity;

use DateInterval;
use DateTime;
use Exception;
use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\Login;
use MarketPlace\Common\Domain\ValueObject\Password;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\UserStatus;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Partners\Auth\Domain\Events\SendConfirmationCodeForPhoneNumberEvent;
use MarketPlace\Partners\Auth\Domain\Events\UserAuthorizedEvent;
use MarketPlace\Partners\Auth\Domain\ValueObject\Email;
use MarketPlace\Partners\Auth\Domain\ValueObject\Phone;
use MarketPlace\Partners\Auth\Domain\ValueObject\UserName;
use MarketPlace\Partners\Auth\Infrastructure\Exception\SendSmsThrottleException;

class User implements AggregateRoot
{
    use EventTrait;

    private Uuid $uuid;
    private Login $login;
    private ?UserName $userName;
    private ?Phone $phone;
    private ?Email $email;
    private ?Password $password;
    private UserStatus $status;

    public function __construct(
        Uuid         $uuid,
        Login        $login,
        ?UserName  $userName = null,
        ?Phone       $phone = null,
        ?Email       $email = null,
        ?Password    $password = null,
        ?UserStatus  $status = null,
    )
    {
        $this->uuid = $uuid;
        $this->login = $login;
        $this->userName = $userName;
        $this->phone = $phone;
        $this->email = $email;
        $this->password = $password;
        $this->status = $status ?? UserStatus::createActiveStatus();
    }

    public static function createViaPhone(Phone $phone): self
    {
        return new self(
            uuid: Uuid::next(),
            login: Login::generate(),
            phone: $phone,
        );
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function authorize(): void
    {
        $this->recordEvent(new UserAuthorizedEvent($this));
    }

    /**
     * @return Login
     */
    public function getLogin(): Login
    {
        return $this->login;
    }

    public function getUserName(): ?UserName
    {
        return $this->userName;
    }

    public function getPhone(): ?Phone
    {
        return $this->phone;
    }

    public function getEmail(): ?Email
    {
        return $this->email;
    }

    /**
     * @return Password|null
     */
    public function getPassword(): ?Password
    {
        return $this->password;
    }


    /**
     * @throws SendSmsThrottleException
     * @throws Exception
     */
    public function sendSmsConfirmationCode(?ConfirmationCode $code = null): void
    {
        if (is_null($this->getPhone()?->getSendAt())) {
            $this->phone->setSendAt(SendAt::now());
        } elseif (
            $this->phone->getSendAt()?->getDateTime()->add(new DateInterval('PT1M'))->getTimestamp()
            > (new DateTime())->getTimestamp()
        ) {
            throw new SendSmsThrottleException();
        }

        $this->phone->setSendAt(SendAt::now());
        $code = $code ?? ConfirmationCode::generate();
        $this->phone->setCode($code->getCode());

        $this->recordEvent(new SendConfirmationCodeForPhoneNumberEvent($this->phone));
    }

    /**
     * @throws SendSmsThrottleException
     * @throws Exception
     */
    public function sendEmailConfirmationCode(?ConfirmationCode $code = null): void
    {
        if (is_null($this->getEmail()?->getSendAt())) {
            $this->email->setSendAt(SendAt::now());
        } elseif (
            $this->email->getSendAt()?->getDateTime()->add(new DateInterval('PT1M'))->getTimestamp()
            > (new DateTime())->getTimestamp()
        ) {
            throw new SendSmsThrottleException();
        }

        $this->email->setSendAt(SendAt::now());
        $code = $code ?? ConfirmationCode::generate();
        $this->email->setCode($code->getCode());

        $this->recordEvent(new SendConfirmationCodeForPhoneNumberEvent($this->phone));
    }

    public function smsCodeCorrect(string $code): bool
    {
        return $this->getPhone()?->getCode() === $code;
    }

    public function emailCodeCorrect(string $code): bool
    {
        return $this->getEmail()?->getCode() === $code;
    }

    public function clearTempData(): void
    {
        $this->getPhone()?->clearTempData();
        $this->getEmail()?->clearTempData();
    }
}

