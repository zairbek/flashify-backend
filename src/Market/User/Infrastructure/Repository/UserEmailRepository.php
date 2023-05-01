<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Infrastructure\Repository;

use App\Models\UserEmail as UserEmailDb;
use App\Models\UserPhone;
use Carbon\Carbon;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Domain\ValueObject\VerifiedAt;
use MarketPlace\Common\Infrastructure\Service\Hydrator;
use MarketPlace\Market\User\Domain\Entity\UserEmail;
use MarketPlace\Market\User\Domain\Entity\UserPhoneNumber;
use MarketPlace\Market\User\Domain\Repository\UserEmailRepositoryInterface;
use MarketPlace\Market\User\Domain\Repository\UserPhoneRepositoryInterface;
use MarketPlace\Market\User\Domain\ValueObject\UserId;
use MarketPlace\Market\User\Infrastructure\Exception\UserEmailNotFoundException;
use MarketPlace\Market\User\Infrastructure\Exception\UserPhoneNumberNotFoundException;

class UserEmailRepository implements UserEmailRepositoryInterface
{
    private Hydrator $hydrator;

    public function __construct()
    {
        $this->hydrator = new Hydrator();
    }

    /**
     * @param Email $email
     * @return UserEmail
     * @throws UserEmailNotFoundException
     */
    public function findUserEmail(Email $email): UserEmail
    {
        /** @var UserEmailDb $userEmail */
        $userEmail = UserEmailDb::query()
            ->where('email', $email->getEmail())
            ->first();

        if (is_null($userEmail)) {
            throw new UserEmailNotFoundException();
        }

        return $this->hydrator($userEmail);
    }

//    public function create(UserPhoneNumber $userPhoneNumber): void
//    {
//        UserPhone::create([
//            'uuid' => $userPhoneNumber->getUuid()->getId(),
//            'region_iso_code' => $userPhoneNumber->getPhone()->getRegionCode(),
//            'phone_number' => $userPhoneNumber->getPhone()->toString(),
//            'user_id' => $userPhoneNumber->getUserId()?->getUserId()->getId(),
//            'confirmation_code' => $userPhoneNumber->getConfirmationCode()?->getCode(),
//            'send_at' => $userPhoneNumber->getSendAt()?->toIsoFormat(),
//        ]);
//    }
//
    /**
     * @param UserEmail $userEmail
     * @return void
     */
    public function update(UserEmail $userEmail): void
    {
        UserEmailDb::query()
            ->where('email', $userEmail->getEmail()->getEmail())
            ->update([
                'confirmation_code' => $userEmail->getConfirmationCode()?->getCode(),
                'send_at' => $userEmail->getSendAt()?->toIsoFormat(),
                'email_verified_at' => $userEmail->getVerifiedAt()?->toIsoFormat(),
                'user_uuid' => $userEmail->getUserUuid()->getUserId()->getId(),
            ]);
    }
//
//    /**
//     * @param Uuid $uuid
//     * @return UserPhoneNumber
//     * @throws UserPhoneNumberNotFoundException
//     */
//    public function find(Uuid $uuid): UserPhoneNumber
//    {
//        /** @var UserPhone $userPhone */
//        $userPhone = UserPhone::query()->where('uuid', $uuid->getId())->first();
//
//        if (is_null($userPhone)) {
//            throw new UserPhoneNumberNotFoundException();
//        }
//
//        return $this->hydrator($userPhone);
//    }

    public function hydrator(UserEmailDb $userEmailDb): UserEmail
    {
        return $this->hydrator->hydrate(UserEmail::class, [
            'uuid' => new Uuid($userEmailDb->uuid),
            'email' => new Email($userEmailDb->email),
            'confirmationCode' => $userEmailDb->confirmation_code ? new ConfirmationCode($userEmailDb->confirmation_code) : null,
            'sendAt' => $userEmailDb->send_at ? new SendAt($userEmailDb->send_at->toDateTime()) : null,
            'verifiedAt' => $userEmailDb->email_verified_at ? new VerifiedAt($userEmailDb->email_verified_at->toDateTime()) : null,
            'userUuid' => new UserId(new Uuid($userEmailDb->user_uuid)),
        ]);
    }

}
