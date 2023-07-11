<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Domain\Events;

use App;
use MarketPlace\Common\Domain\Events\EventInterface;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Partners\User\Domain\Entity\RequestCode;

class SendConfirmationCodeEvent implements EventInterface
{
    public function __construct(
        public RequestCode $requestCode
    )
    {
    }
}
