<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Infrastructure\Repository;

use App\Models\UserPhone;
use Carbon\Carbon;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Hydrator;
use MarketPlace\Market\User\Domain\Entity\UserPhoneNumber;
use MarketPlace\Market\User\Domain\Repository\UserPhoneRepositoryInterface;
use MarketPlace\Market\User\Domain\ValueObject\UserId;
use MarketPlace\Market\User\Infrastructure\Exception\UserPhoneNumberNotFoundException;

class UserPhoneRepository implements UserPhoneRepositoryInterface
{
    private Hydrator $hydrator;

    public function __construct()
    {
        $this->hydrator = new Hydrator();
    }

    /**
     * @param Phone $phone
     * @return UserPhoneNumber
     * @throws UserPhoneNumberNotFoundException
     */
    public function findUserPhone(Phone $phone): UserPhoneNumber
    {
        /** @var UserPhone $userPhone */
        $userPhone = UserPhone::query()
            ->where('region_iso_code', $phone->getRegionCode())
            ->where('phone_number', $phone->toString())
            ->first();

        if (is_null($userPhone)) {
            throw new UserPhoneNumberNotFoundException();
        }

        return $this->hydrator($userPhone);
    }

    public function create(UserPhoneNumber $userPhoneNumber): void
    {
        UserPhone::create([
            'uuid' => $userPhoneNumber->getUuid()->getId(),
            'region_iso_code' => $userPhoneNumber->getPhone()->getRegionCode(),
            'phone_number' => $userPhoneNumber->getPhone()->toString(),
            'user_id' => $userPhoneNumber->getUserId()?->getUserId()->getId(),
            'confirmation_code' => $userPhoneNumber->getConfirmationCode()?->getCode(),
            'send_at' => $userPhoneNumber->getSendAt()?->toIsoFormat(),
        ]);
    }

    /**
     * @param UserPhoneNumber $userPhoneNumber
     * @return void
     */
    public function update(UserPhoneNumber $userPhoneNumber): void
    {
        UserPhone::query()
            ->where('region_iso_code', $userPhoneNumber->getPhone()->getRegionCode())
            ->where('phone_number', $userPhoneNumber->getPhone()->toString())
            ->update([
                'region_iso_code' => $userPhoneNumber->getPhone()->getRegionCode(),
                'phone_number' => $userPhoneNumber->getPhone()->toString(),
                'user_id' => $userPhoneNumber->getUserId()?->getUserId()->getId(),
                'confirmation_code' => $userPhoneNumber->getConfirmationCode()?->getCode(),
                'send_at' => $userPhoneNumber->getSendAt()?->toIsoFormat(),
            ]);
    }

    /**
     * @param Uuid $uuid
     * @return UserPhoneNumber
     * @throws UserPhoneNumberNotFoundException
     */
    public function find(Uuid $uuid): UserPhoneNumber
    {
        /** @var UserPhone $userPhone */
        $userPhone = UserPhone::query()->where('uuid', $uuid->getId())->first();

        if (is_null($userPhone)) {
            throw new UserPhoneNumberNotFoundException();
        }

        return $this->hydrator($userPhone);
    }

    public function hydrator(UserPhone $userPhoneDb): UserPhoneNumber
    {
        return $this->hydrator->hydrate(UserPhoneNumber::class, [
            'uuid' => new Uuid($userPhoneDb->uuid),
            'phone' => Phone::fromString(regionCode: $userPhoneDb->region_iso_code, phoneString: $userPhoneDb->phone_number),
            'createdAt' => new CreatedAt($userPhoneDb->created_at->toDateTime()),
            'userId' => $userPhoneDb->user_id ? new UserId(new Uuid($userPhoneDb->user_id)) : null,
            'confirmationCode' => $userPhoneDb->confirmation_code ? new ConfirmationCode($userPhoneDb->confirmation_code) : null,
            'sendAt' => $userPhoneDb->send_at ? new SendAt($userPhoneDb->send_at->toDateTime()) : null
        ]);
    }

}
