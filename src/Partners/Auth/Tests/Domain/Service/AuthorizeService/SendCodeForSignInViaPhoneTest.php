<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Tests\Domain\Service\AuthorizeService;

use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Tests\TestCase;
use MarketPlace\Market\Auth\Application\Dto\SendCodeForSignInViaPhoneDto;
use MarketPlace\Market\Auth\Application\Service\AuthorizeService;
use MarketPlace\Market\Auth\Domain\Adapter\UserAdapterInterface;
use MarketPlace\Market\Auth\Domain\Entity\PhoneNumber;

class SendCodeForSignInViaPhoneTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->phone = Phone::fromString('KG', '+996772119663');

        $this->userAdapter = $this->createMock(UserAdapterInterface::class);
        $this->userAdapter->method('findUserPhone')->willReturn(new PhoneNumber(
            uuid: Uuid::next(),
            phone: $this->phone,
            createdAt: CreatedAt::now(),
            sendAt: new SendAt((new \DateTime())->sub(new \DateInterval('PT60S')))
        ));
    }

    public function testSuccessWhenUserPhoneExists()
    {
        $authorizeService = new AuthorizeService($this->userAdapter);
        $authorizeService->sendCodeForSignInViaPhone(new SendCodeForSignInViaPhoneDto(
            $this->phone->getRegionCode(), $this->phone->getInternationalFormat()
        ));

        dd($authorizeService);
    }
}
