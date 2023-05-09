<?php

declare(strict_types=1);

namespace MarketPlace\Backoffice\Auth\Infrastructure\Adapter;

use MarketPlace\Backoffice\Auth\Domain\Entity\User;
use MarketPlace\Backoffice\Auth\Infrastructure\Exception\UserCredentialsIncorrectException;
use MarketPlace\Backoffice\User\Infrastructure\Api\UserApi;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Password;
use MarketPlace\Common\Domain\ValueObject\PersonName;
use MarketPlace\Common\Domain\ValueObject\UserStatus;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Hydrator;
use MarketPlace\Backoffice\Auth\Domain\Adapter\UserAdapterInterface;

class UserAdapter implements UserAdapterInterface
{
    private UserApi $api;
    private Hydrator $hydrator;

    public function __construct()
    {
        $this->api = new UserApi();
        $this->hydrator = new Hydrator();
    }

    /**
     * @inheritDoc
     */
    public function findUser(Uuid $uuid): User
    {
        $user = $this->api->findUser($uuid->getId());

        return $this->userHydrator($user);
    }

    /**
     * @param Email $email
     * @param Password $password
     * @return User
     * @throws UserCredentialsIncorrectException
     */
    public function getByCredentials(Email $email, Password $password): User
    {
        $user = $this->api->getByCredentials($email->getEmail(), $password->getPassword());

        if (empty($user)) {
            throw new UserCredentialsIncorrectException();
        }

        return $this->userHydrator($user);
    }

    private function userHydrator(array $user): User
    {
        return $this->hydrator->hydrate(User::class, [
            'id' => new Uuid($user['uuid']),
            'name' => new PersonName(
                firstName: $user['name']['firstName'],
                lastName: $user['name']['lastName'],
            ),
            'email' => new Email($user['email']),
            'status' => new UserStatus($user['status']),
        ]);
    }
}
