<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Partner\Infrastructure\Repository;

use App\Models\User as UserDB;
use Auth;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Login;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Sex;
use MarketPlace\Common\Domain\ValueObject\UserStatus;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Domain\ValueObject\VerifiedAt;
use MarketPlace\Common\Infrastructure\Service\Hydrator;
use MarketPlace\Market\User\Domain\Entity\User;
use MarketPlace\Market\User\Domain\Entity\UserEmail;
use MarketPlace\Market\User\Domain\Entity\UserPhoneNumber;
use MarketPlace\Market\User\Domain\ValueObject\UserId;
use MarketPlace\Market\User\Domain\ValueObject\UserName;
use MarketPlace\Market\User\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Market\User\Infrastructure\Exception\UserUnauthenticatedException;
use MarketPlace\Partners\Partner\Domain\Repository\AccountRepositoryInterface;

class AccountRepository implements AccountRepositoryInterface
{

    private Hydrator $hydrator;

    public function __construct()
    {
        $this->hydrator = new Hydrator();
    }

    public function create(User $user): void
    {
        UserDB::create([
            'uuid' => $user->getUuid()->getId(),
            'login' => $user->getLogin()->getLogin(),
        ]);
    }

    /**
     * @throws UserNotFoundException
     */
    public function find(Uuid $uuid): User
    {
        /** @var UserDB $userDb */
        $userDb = UserDB::query()->with(['phone', 'email'])->where('uuid', $uuid->getId())->first();

        if (is_null($userDb)) {
            throw new UserNotFoundException();
        }

        return $this->userHydrator($userDb);
    }

    /**
     * @return User
     * @throws UserUnauthenticatedException
     */
    public function me(): User
    {
        /** @var UserDB $userDb */
        $userDb = Auth::user();

        if (is_null($userDb)) {
            throw new UserUnauthenticatedException();
        }

        $userDb->load(['phone', 'email']);

        return $this->userHydrator($userDb);
    }

    private function userHydrator(UserDB $userDb): User
    {
        return $this->hydrator->hydrate(User::class, [
            'uuid' => new Uuid($userDb->uuid),
            'login' => new Login($userDb->login),
            'userName' => new UserName(
                firstName: $userDb->first_name,
                lastName: $userDb->last_name,
                middleName: $userDb->middle_name,
            ),
            'phone' => $userDb->phone
                ? $this->hydrator->hydrate(UserPhoneNumber::class, [
                    'uuid' => new Uuid($userDb->phone->uuid),
                    'phone' => Phone::fromString($userDb->phone->region_iso_code, $userDb->phone->phone_number),
                    'createdAt' => new CreatedAt($userDb->phone->created_at->toDateTime()),
                    'userId' => $userDb->phone->user_id ? new UserId(new Uuid($userDb->phone->user_id)) : null,
                    'confirmationCode' => $userDb->phone->confirmation_code ? new ConfirmationCode($userDb->phone->confirmation_code) : null,
                    'sendAt' => $userDb->phone->send_at ? new SendAt($userDb->phone->send_at->toDateTime()) : null,
                ])
                : null,
            'email' => $userDb->email
                ? $this->hydrator->hydrate(UserEmail::class, [
                    'uuid' => new Uuid($userDb->email->uuid),
                    'email' => new Email($userDb->email->email),
                    'confirmationCode' => $userDb->email->confirmation_code ? new ConfirmationCode($userDb->email->confirmation_code) : null,
                    'sendAt' => $userDb->email->send_at ? new SendAt($userDb->email->send_at->toDateTime()) : null,
                    'verifiedAt' => $userDb->email->email_verified_at ? new VerifiedAt($userDb->email->email_verified_at->toDateTime()) : null,
                    'userUuid' => $userDb->email->user_uuid ? new UserId(new Uuid($userDb->email->user_uuid)) : null,
                ])
                : null,
            'sex' => $userDb->sex ? new Sex($userDb->sex) : null,
            'status' => new UserStatus($userDb->status),
        ]);
    }
}
