<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Auth\Domain\Entity;

use MarketPlace\Backoffice\Auth\Domain\Events\UserAuthorizedEvent;
use MarketPlace\Common\Domain\Entity\AggregateRoot;
use MarketPlace\Common\Domain\Entity\EventTrait;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Password;
use MarketPlace\Common\Domain\ValueObject\PersonName;
use MarketPlace\Common\Domain\ValueObject\UserStatus;
use MarketPlace\Common\Domain\ValueObject\Uuid;

class User implements AggregateRoot
{
    use EventTrait;

    private Uuid $id;
    private ?PersonName $name;
    private Email $email;
    private ?Password $password;
    private UserStatus $status;

    public function __construct(
        Uuid         $id,
        ?PersonName  $name = null,
        Email       $email = null,
        ?Password    $password = null,
        ?UserStatus  $status = null,
    )
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->status = $status ?? UserStatus::createActiveStatus();
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
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

    /**
     * @return PersonName|null
     */
    public function getName(): ?PersonName
    {
        return $this->name;
    }

    /**
     * @return Email
     */
    public function getEmail(): Email
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
}

