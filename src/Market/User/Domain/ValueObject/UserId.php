<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Domain\ValueObject;

use MarketPlace\Common\Domain\ValueObject\Uuid;

class UserId
{
    private Uuid $userId;

    public function __construct(Uuid $userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return Uuid
     */
    public function getUserId(): Uuid
    {
        return $this->userId;
    }
}
