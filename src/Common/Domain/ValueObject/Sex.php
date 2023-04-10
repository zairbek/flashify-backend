<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

use Webmozart\Assert\Assert;

class Sex
{
    const MALE = 'male';
    const FEMALE = 'female';
    private string $sex;

    public function __construct(string $sex)
    {
        Assert::inArray($sex, [self::MALE, self::FEMALE]);

        $this->sex = $sex;
    }

    /**
     * @return string
     */
    public function getSex(): string
    {
        return $this->sex;
    }
}
