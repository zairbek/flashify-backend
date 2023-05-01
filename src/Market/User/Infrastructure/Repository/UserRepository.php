<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Infrastructure\Repository;

use App\Models\User as UserDB;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Login;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Sex;
use MarketPlace\Common\Domain\ValueObject\UserStatus;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Hydrator;
use MarketPlace\Market\User\Domain\Entity\User;
use MarketPlace\Market\User\Domain\Entity\UserPhoneNumber;
use MarketPlace\Market\User\Domain\Repository\UserRepositoryInterface;
use MarketPlace\Market\User\Domain\ValueObject\UserId;
use MarketPlace\Market\User\Domain\ValueObject\UserName;
use MarketPlace\Market\User\Infrastructure\Exception\UserNotFoundException;

class UserRepository implements UserRepositoryInterface
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
        $userDb = UserDB::query()->with('phone')->where('uuid', $uuid->getId())->first();

        if (is_null($userDb)) {
            throw new UserNotFoundException();
        }

        return $this->hydrator->hydrate(User::class, [
            'uuid' => new Uuid($userDb->uuid),
            'login' => new Login($userDb->login),
            'userName' => new UserName(
                firstName: $userDb->first_name,
                lastName: $userDb->last_name,
                middleName: $userDb->middle_name,
            ),
            'phone' => $userDb->phone ?
                $this->hydrator->hydrate(UserPhoneNumber::class, [
                     'uuid' => new Uuid($userDb->phone->uuid),
                     'phone' => Phone::fromString($userDb->phone->region_iso_code, $userDb->phone->phone_number),
                     'createdAt' => new CreatedAt($userDb->phone->created_at->toDateTime()),
                     'userId' => $userDb->phone->user_id ? new UserId(new Uuid($userDb->phone->user_id)) : null,
                     'confirmationCode' => $userDb->phone->confirmation_code ? new ConfirmationCode($userDb->phone->confirmation_code) : null,
                     'sendAt' => $userDb->phone->send_at ? new SendAt($userDb->phone->send_at->toDateTime()) : null,
                ])
                : null,
            'sex' => $userDb->sex ? new Sex($userDb->sex) : null,
            'email' => $userDb->email ? new Email($userDb->email) : null,
            'status' => new UserStatus($userDb->status),
        ]);
    }
}
