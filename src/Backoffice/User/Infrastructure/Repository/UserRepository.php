<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\User\Infrastructure\Repository;

use App\Models\Admin as AdminDB;
use Auth;
use MarketPlace\Backoffice\User\Domain\Entity\User;
use MarketPlace\Backoffice\User\Domain\Repository\UserRepositoryInterface;
use MarketPlace\Backoffice\User\Domain\ValueObject\UserName;
use MarketPlace\Backoffice\User\Infrastructure\Exception\UserCredentialsIncorrectException;
use MarketPlace\Backoffice\User\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Backoffice\User\Infrastructure\Exception\UserUnauthenticatedException;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Password;
use MarketPlace\Common\Domain\ValueObject\Sex;
use MarketPlace\Common\Domain\ValueObject\UserStatus;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Hydrator;

class UserRepository implements UserRepositoryInterface
{

    private Hydrator $hydrator;

    public function __construct()
    {
        $this->hydrator = new Hydrator();
    }

    public function create(User $user): void
    {
        AdminDB::create([
            'uuid' => $user->getUuid()->getId(),
            'email' => $user->getEmail()->getEmail(),
            'password' => $user->getPassword()->getPassword(),
        ]);
    }

    /**
     * @throws UserNotFoundException
     */
    public function find(Uuid $uuid): User
    {
        /** @var AdminDB $userDb */
        $userDb = AdminDB::query()->where('uuid', $uuid->getId())->first();

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
        /** @var AdminDB $userDb */
        $userDb = Auth::user();

        if (is_null($userDb)) {
            throw new UserUnauthenticatedException();
        }

        return $this->userHydrator($userDb);
    }

    /**
     * @param Email $email
     * @param Password $password
     * @return User
     * @throws UserCredentialsIncorrectException
     */
    public function getByCredentials(Email $email, Password $password): User
    {
        /** @var AdminDB $userDb */
        $userDb = AdminDB::query()
            ->where('email', $email->getEmail())
            ->first();

        if (! password_verify($password->getPassword(), $userDb->getAuthPassword())) {
            throw new UserCredentialsIncorrectException();
        }

        return $this->userHydrator($userDb);
    }

    private function userHydrator(AdminDB $userDb): User
    {
        return $this->hydrator->hydrate(User::class, [
            'uuid' => new Uuid($userDb->uuid),
            'userName' => new UserName(
                firstName: $userDb->first_name,
                lastName: $userDb->last_name,
            ),
            'email' => new Email($userDb->email),
            'sex' => $userDb->sex ? new Sex($userDb->sex) : null,
            'status' => new UserStatus($userDb->status),
        ]);
    }
}
