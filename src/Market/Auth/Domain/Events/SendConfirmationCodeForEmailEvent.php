<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Domain\Events;

use App;
use MarketPlace\Common\Domain\Events\EventInterface;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\Email;

class SendConfirmationCodeForEmailEvent implements EventInterface
{
    public ConfirmationCode $confirmationCode;
    public Email $email;

    public function __construct(Email $email, ConfirmationCode $confirmationCode)
    {
        $this->email = $email;
        $this->confirmationCode = $confirmationCode;
    }
}
