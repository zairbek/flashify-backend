<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject\Translates;

class Translate
{
    private string $code;
    private string $value;

    public function __construct(string $code, string $value)
    {
        $this->code = $code;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }
}
