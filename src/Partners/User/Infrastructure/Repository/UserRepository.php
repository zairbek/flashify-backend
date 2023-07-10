<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Infrastructure\Repository;

use App\Models\Account;
use Auth;
use DateTime;
use Exception;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Login;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Domain\ValueObject\VerifiedAt;
use MarketPlace\Common\Infrastructure\Service\Hydrator;
use MarketPlace\Market\User\Domain\Entity\UserEmail;
use MarketPlace\Market\User\Domain\Entity\UserPhoneNumber;
use MarketPlace\Market\User\Domain\ValueObject\UserId;
use MarketPlace\Partners\User\Domain\ValueObject\Email;
use MarketPlace\Partners\User\Domain\ValueObject\Phone;
use MarketPlace\Partners\User\Domain\ValueObject\UserName;
use MarketPlace\Partners\User\Domain\Entity\User;
use MarketPlace\Partners\User\Domain\Repository\UserRepositoryInterface;
use MarketPlace\Partners\User\Domain\ValueObject\UserStatus;
use MarketPlace\Partners\User\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\User\Infrastructure\Exception\UserUnauthenticatedException;

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
    public function update(User $user): void
    {
        $account = Account::query()->firstWhere('uuid', $user->getUuid()->getId());

        if (is_null($account)) {
            throw new UserNotFoundException();
        }

        $confirmationCode = [
            'email' => [
                'code' => $user->getEmail()?->getCode(),
                'sendAt' => $user->getEmail()?->getSendAt()?->toIsoFormat(),
            ],
            'phone' => [
                'code' => $user->getPhone()?->getCode(),
                'sendAt' => $user->getPhone()?->getSendAt()?->toIsoFormat(),
            ],
        ];

        $account->update([
            'login' => $user->getLogin()->getLogin(),
            'first_name' => $user->getAccountName()?->getFirstName(),
            'last_name' => $user->getAccountName()?->getLastName(),
            'region_iso_code' => $user->getPhone()?->getRegionCode(),
            'phone_number' => $user->getPhone()?->toString(),
            'email' => $user->getEmail()?->getEmail(),
            'status' => $user->getStatus()->getStatus(),
            'confirmation_code' => $confirmationCode,
        ]);
    }

    /**
     * @throws UserNotFoundException
     */
    public function find(Uuid $uuid): User
    {
        /** @var Account $userDb */
        $userDb = Account::query()->where('uuid', $uuid->getId())->first();

        if (is_null($userDb)) {
            throw new UserNotFoundException();
        }

        return $this->userHydrator($userDb);
    }

    /**
     * @return User
     * @throws UserUnauthenticatedException
     * @throws Exception
     */
    public function me(): User
    {
        /** @var Account $userDb */
        $userDb = Auth::user();

        if (is_null($userDb)) {
            throw new UserUnauthenticatedException();
        }

        return $this->userHydrator($userDb);
    }

    /**
     * @throws UserNotFoundException
     * @throws Exception
     */
    public function findByPhone(Phone $phone): User
    {
        /** @var Account $account */
        $account = Account::query()->firstWhere('phone_number', '=', $phone->toString());

        if (is_null($account)) {
            throw new UserNotFoundException();
        }

        return $this->userHydrator($account);
    }

    /**
     * @throws UserNotFoundException
     * @throws Exception
     */
    public function findByEmail(Email $email): User
    {
        /** @var Account $account */
        $account = Account::query()->firstWhere('email', '=', $email->getEmail());

        if (is_null($account)) {
            throw new UserNotFoundException();
        }

        return $this->userHydrator($account);
    }

    /**
     * @throws Exception
     */
    private function userHydrator(Account $userDb): User
    {
        $phoneCodeSendAt = null;
        if (
            isset($userDb->confirmation_code['phone']['sendAt'])
            && !is_null($userDb->confirmation_code['phone']['sendAt'])
        ) {
            $phoneCodeSendAt = SendAt::fromIsoFormat($userDb->confirmation_code['phone']['sendAt']);
        }

        $emailCodeSendAt = null;
        if (
            isset($userDb->confirmation_code['email']['sendAt'])
            && !is_null($userDb->confirmation_code['email']['sendAt'])
        ) {
            $emailCodeSendAt = SendAt::fromIsoFormat($userDb->confirmation_code['email']['sendAt']);
        }

        return $this->hydrator->hydrate(User::class, [
            'uuid' => new Uuid($userDb->uuid),
            'login' => new Login($userDb->login),
            'userName' => new UserName(
                firstName: $userDb->first_name,
                lastName: $userDb->last_name,
            ),
            'phone' => $userDb->phone_number
                ? Phone::fromString(
                    $userDb->region_iso_code,
                    $userDb->phone_number,
                    $userDb->confirmation_code['phone']['code'] ?? null,
                    $phoneCodeSendAt
                )
                : null,
            'email' => $userDb->email
                ? new Email(
                    $userDb->email,
                    $userDb->confirmation_code['email']['code'] ?? null,
                    $emailCodeSendAt
                ) : null,
            'status' => new UserStatus($userDb->status),
        ]);
    }
}
