<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Application\Service;

use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Login;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Domain\ValueObject\VerifiedAt;
use MarketPlace\Common\Infrastructure\Service\Hydrator;
use MarketPlace\Market\User\Application\Dto\CreateUserFromPhoneDto;
use MarketPlace\Market\User\Application\Dto\CreateUserPhoneDto;
use MarketPlace\Market\User\Application\Dto\FindUserEmailDto;
use MarketPlace\Market\User\Application\Dto\FindUserPhoneDto;
use MarketPlace\Market\User\Application\Dto\UpdateUserEmailDto;
use MarketPlace\Market\User\Application\Dto\UpdateUserPhoneDto;
use MarketPlace\Market\User\Domain\Entity\User;
use MarketPlace\Market\User\Domain\Entity\UserEmail;
use MarketPlace\Market\User\Domain\Entity\UserPhoneNumber;
use MarketPlace\Market\User\Domain\Repository\UserEmailRepositoryInterface;
use MarketPlace\Market\User\Domain\Repository\UserPhoneRepositoryInterface;
use MarketPlace\Market\User\Domain\Repository\UserRepositoryInterface;
use MarketPlace\Market\User\Domain\ValueObject\UserId;
use MarketPlace\Market\User\Infrastructure\Exception\UserEmailNotFoundException;
use MarketPlace\Market\User\Infrastructure\Exception\UserPhoneNumberNotFoundException;

class UserEmailService
{
    private Hydrator $hydrator;
    private UserEmailRepositoryInterface $userEmailRepository;
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository, UserEmailRepositoryInterface $userEmailRepository)
    {
        $this->hydrator = new Hydrator();
        $this->userEmailRepository = $userEmailRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @param FindUserEmailDto $dto
     * @return UserEmail
     * @throws UserEmailNotFoundException
     */
    public function findUserEmail(FindUserEmailDto $dto): UserEmail
    {
        return $this->userEmailRepository->findUserEmail(new Email($dto->email));
    }

//    public function createUserPhone(CreateUserPhoneDto $dto)
//    {
//        $userPhoneNumber = $this->hydrator->hydrate(UserPhoneNumber::class, [
//            'uuid' => new Uuid($dto->uuid),
//            'phone' => Phone::fromString($dto->regionIsoCode, $dto->phoneNumber),
//            'createdAt' => CreatedAt::fromIsoFormat($dto->createdAt),
//            'userId' => $dto->userId ? new UserId(new Uuid($dto->userId)) : null,
//            'confirmationCode' => $dto->confirmationCode ? new ConfirmationCode($dto->confirmationCode) : null,
//            'sendAt' => $dto->sendAt ? SendAt::fromIsoFormat($dto->sendAt) : null
//        ]);
//
//        $this->userPhoneRepository->create($userPhoneNumber);
//    }
//
    public function updateUserEmail(UpdateUserEmailDto $dto): void
    {
        $userEmail = $this->hydrator->hydrate(UserEmail::class, [
            'uuid' => new Uuid($dto->uuid),
            'email' => new Email($dto->email),
            'userUuid' => new UserId(new Uuid($dto->userUuid)),
            'confirmationCode' => $dto->confirmationCode ? new ConfirmationCode($dto->confirmationCode) : null,
            'sendAt' => $dto->sendAt ? SendAt::fromIsoFormat($dto->sendAt) : null,
            'verifiedAt' => $dto->verifiedAt ? VerifiedAt::fromIsoFormat($dto->verifiedAt) : null,
        ]);

        $this->userEmailRepository->update($userEmail);
    }
//
//    public function createUser(CreateUserFromPhoneDto $dto): void
//    {
//        $userPhoneNumber = $this->userPhoneRepository->find(new Uuid($dto->phoneUuid));
//
//        $userPhoneNumber->setUserId(
//            userId: new UserId(new Uuid($dto->uuid)),
//        );
//
//        $user = User::createFromPhone(new Uuid($dto->uuid), new Login($dto->login));
//        $this->userRepository->create($user);
//        $this->userPhoneRepository->update($userPhoneNumber);
//    }
}
