<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Domain\ValueObject;

use DateTime;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use MarketPlace\Common\Domain\ValueObject\Phone as CommonPhone;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

class Phone extends CommonPhone
{
    private ?string $code;
    private ?SendAt $sendAt;

    public function __construct(
        string    $regionCode,
        int       $countryCode,
        string    $number,
        ?string      $code = null,
        ?SendAt $sendAt = null
    )
    {
        parent::__construct($regionCode, $countryCode, $number);
        $this->code = $code;
        $this->sendAt = $sendAt;
    }

    public static function fromString(
        string    $regionCode,
        string    $phoneString,
        ?string      $code = null,
        ?SendAt $sendAt = null
    ): self
    {
        Assert::notEmpty($phoneString);

        try {
            $phoneNumberUtil = PhoneNumberUtil::getInstance();
            $phone = $phoneNumberUtil->parse($phoneString, $regionCode);

            Assert::notNull($phone);
            Assert::true($phoneNumberUtil->isValidNumber($phone), 'Phone number is not valid');

            return new self($regionCode, $phone->getCountryCode(), $phone->getNationalNumber(), $code, $sendAt);
        } catch (NumberParseException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
        }
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
