<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Domain\ValueObject;

use DateTime;
use MarketPlace\Common\Domain\ValueObject\Email as CommonEmail;
use MarketPlace\Common\Domain\ValueObject\SendAt;

class Email extends CommonEmail
{
    private ?string $code;
    private ?SendAt $sendAt;

    public function __construct(string $email, ?string $code = null, ?SendAt $sendAt = null)
    {
        parent::__construct($email);
        $this->code = $code;
        $this->sendAt = $sendAt;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getSendAt(): ?SendAt
    {
        return $this->sendAt;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function setSendAt(SendAt $sendAt): void
    {
        $this->sendAt = $sendAt;
    }

    public function clearTempData(): void
    {
        $this->code = null;
        $this->sendAt = null;
    }
}
