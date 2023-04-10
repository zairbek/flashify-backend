<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

use Exception;
use Webmozart\Assert\Assert;

class ConfirmationCode
{
    private string $code;

    public function __construct(string $code)
    {
        Assert::stringNotEmpty($code);

        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @throws Exception
     */
    public static function generate(): self
    {
        $code = self::codeGenerator();

        return new self($code);
    }

    /**
     * @throws Exception
     */
    private static function codeGenerator(): string
    {
        $number = random_int(0, 999999);
        return str_pad((string) $number,6, "0",STR_PAD_LEFT);
    }
}
