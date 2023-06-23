<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Application\Service;

use Exception;
use MarketPlace\Common\Domain\Events\EventDispatcher;
use MarketPlace\Common\Domain\ValueObject\Login;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Hydrator;
use MarketPlace\Partners\User\Application\Dto\FindUserByPhoneDto;
use MarketPlace\Partners\User\Application\Dto\UpdateUserDto;
use MarketPlace\Partners\User\Domain\Entity\User;
use MarketPlace\Partners\User\Domain\Repository\UserRepositoryInterface;
use MarketPlace\Partners\User\Domain\ValueObject\Email;
use MarketPlace\Partners\User\Domain\ValueObject\Phone;
use MarketPlace\Partners\User\Domain\ValueObject\UserName;
use MarketPlace\Partners\User\Domain\ValueObject\UserStatus;
use MarketPlace\Partners\User\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\User\Infrastructure\Exception\UserUnauthenticatedException;

class UserService
{
    private array $listeners = [

    ];
    private EventDispatcher $eventDispatcher;
    private UserRepositoryInterface $repository;
    private Hydrator $hydrator;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->eventDispatcher = new EventDispatcher($this->listeners);
        $this->repository = $repository;
        $this->hydrator  = new Hydrator();
    }

    /**
     * @throws UserNotFoundException
     * @throws Exception
     */
    public function update(UpdateUserDto $dto): void
    {
        $user = $this->hydrator->hydrate(User::class, [
            'uuid' => new Uuid($dto->uuid),
            'login' => new Login($dto->login),
            'email' => $dto->emailDto
                ? new Email(
                    email: $dto->emailDto->email,
                    code: $dto->emailDto->code,
                    sendAt: $dto->emailDto->sendAt ? SendAt::fromIsoFormat($dto->emailDto->sendAt) : null,
                )
                : null,
            'phone' => $dto->phoneDto
                ? Phone::fromString(
                    regionCode: $dto->phoneDto->regionCode,
                    phoneString: $dto->phoneDto->phone,
                    code: $dto->phoneDto->code,
                    sendAt: $dto->phoneDto->sendAt ? SendAt::fromIsoFormat($dto->phoneDto->sendAt) : null,
                )
                : null,
            'userName' => new UserName($dto->firstName, $dto->lastName),
            'status' => new UserStatus($dto->status),
        ]);

        $this->repository->update($user);
    }

    /**
     * @throws UserNotFoundException
     */
    public function find(Uuid $uuid): User
    {
        return $this->repository->find($uuid);
    }

    /**
     * @throws UserUnauthenticatedException
     */
    public function me(): User
    {
        return $this->repository->me();
    }

    /**
     * @throws UserNotFoundException
     */
    public function findByPhone(FindUserByPhoneDto $dto): User
    {
        return $this->repository->findByPhone(phone: Phone::fromString($dto->regionIsoCode, $dto->phoneNumber));
    }

    /**
     * @throws UserNotFoundException
     */
    public function findByEmail(string $email): User
    {
        return $this->repository->findByEmail(new Email($email));
    }
}
