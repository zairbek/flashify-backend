<?php

declare(strict_types=1);

namespace MarketPlace\Common\Domain\ValueObject;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Webmozart\Assert\Assert;
use Webmozart\Assert\InvalidArgumentException;

class Phone
{
    private string $regionCode;
    private int $countryCode;
    private string $number;
    private PhoneNumber $phoneNumber;
    private PhoneNumberUtil $phoneNumberUtil;

    public function __construct(string $regionCode, int $countryCode, string $number)
    {
        Assert::notEmpty($regionCode);
        Assert::notEmpty($countryCode);
        Assert::notEmpty($number);

        try {
            $this->phoneNumberUtil = PhoneNumberUtil::getInstance();
            $phone = $this->phoneNumberUtil->parse($countryCode . $number, $regionCode);

            Assert::notNull($phone);
            Assert::true($this->phoneNumberUtil->isValidNumber($phone), 'Phone number is not valid');
            $this->phoneNumber = $phone;
        } catch (NumberParseException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
        }

        $this->regionCode = $regionCode;
        $this->countryCode = $countryCode;
        $this->number = $number;
    }

    public function getRegionCode(): string
    {
        return $this->regionCode;
    }

    public function getCountryCode(): int
    {
        return $this->countryCode;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getInternationalFormat(): string
    {
        return $this->phoneNumberUtil->format($this->phoneNumber, PhoneNumberFormat::INTERNATIONAL);
    }

    public function isEqualTo(self $phone): bool
    {
        return $this->getInternationalFormat() === $phone->getInternationalFormat();
    }

    public function toString(): string
    {
        return $this->phoneNumberUtil->format($this->phoneNumber, PhoneNumberFormat::E164);
    }

    public static function fromString(string $regionCode, string $phoneString): self
    {
        Assert::notEmpty($phoneString);

        try {
            $phoneNumberUtil = PhoneNumberUtil::getInstance();
            $phone = $phoneNumberUtil->parse($phoneString, $regionCode);

            Assert::notNull($phone);
            Assert::true($phoneNumberUtil->isValidNumber($phone), 'Phone number is not valid');

            return new self($regionCode, $phone->getCountryCode(), $phone->getNationalNumber());
        } catch (NumberParseException $exception) {
            throw new InvalidArgumentException($exception->getMessage());
        }
    }
}
