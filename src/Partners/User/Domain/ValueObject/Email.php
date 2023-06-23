<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Domain\ValueObject;

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
}
