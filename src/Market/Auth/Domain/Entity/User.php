<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Domain\Entity;

use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Login;
use MarketPlace\Common\Domain\ValueObject\Password;
use MarketPlace\Common\Domain\ValueObject\PersonName;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\UserStatus;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Market\Auth\Domain\Events\UserAuthorizedEvent;

class User implements AggregateRoot
{
    use EventTrait;

    private Uuid $id;
    private Login $login;
    private ?PersonName $personName;
    private ?Phone $phone;
    private ?Email $email;
    private ?Password $password;
    private UserStatus $status;

    public function __construct(
        Uuid $id,
        Login $login,
        ?PersonName $personName = null,
        ?Phone $phone = null,
        ?Email $email = null,
        ?Password $password = null,
        ?UserStatus $status = null,
    )
    {
        $this->id = $id;
        $this->login = $login;
        $this->personName = $personName;
        $this->phone = $phone;
        $this->email = $email;
        $this->password = $password;
        $this->status = $status ?? UserStatus::createActiveStatus();
    }

    public static function createViaPhone(PhoneNumber $phoneNumber): self
    {
        return new self(
            id: Uuid::next(),
            login: Login::generate(),
            phone: $phoneNumber->getPhone(),
        );
    }

    /**
     * @return UserStatus
     */
    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function authorize(): void
    {
        $this->recordEvent(new UserAuthorizedEvent($this));
    }
}

