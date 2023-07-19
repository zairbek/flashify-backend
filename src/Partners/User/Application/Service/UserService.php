<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Application\Service;

use Exception;
use MarketPlace\Common\Domain\Events\EventDispatcher;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\Login;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Hydrator;
use MarketPlace\Partners\User\Application\Dto\ChangeEmailDto;
use MarketPlace\Partners\User\Application\Dto\ChangePhoneDto;
use MarketPlace\Partners\User\Application\Dto\CreateUserDto;
use MarketPlace\Partners\User\Application\Dto\FindUserByPhoneDto;
use MarketPlace\Partners\User\Application\Dto\UpdateUserDto;
use MarketPlace\Partners\User\Application\Dto\UpdateUserNameDto;
use MarketPlace\Partners\User\Domain\Entity\RequestCode;
use MarketPlace\Partners\User\Domain\Entity\User;
use MarketPlace\Partners\User\Domain\Repository\RequestCodeRepositoryInterface;
use MarketPlace\Partners\User\Domain\Repository\UserRepositoryInterface;
use MarketPlace\Partners\User\Domain\ValueObject\Email;
use MarketPlace\Partners\User\Domain\ValueObject\Phone;
use MarketPlace\Partners\User\Domain\ValueObject\UserName;
use MarketPlace\Partners\User\Domain\ValueObject\UserStatus;
use MarketPlace\Partners\User\Infrastructure\Exception\ConfirmationCodeIncorrectException;
use MarketPlace\Partners\User\Infrastructure\Exception\EmailAlreadyRegisteredException;
use MarketPlace\Partners\User\Infrastructure\Exception\PhoneAlreadyRegisteredException;
use MarketPlace\Partners\User\Infrastructure\Exception\RequestCodeNotFoundException;
use MarketPlace\Partners\User\Infrastructure\Exception\RequestCodeThrottlingException;
use MarketPlace\Partners\User\Infrastructure\Exception\UserNotFoundException;
use MarketPlace\Partners\User\Infrastructure\Exception\UserUnauthenticatedException;

class UserService
{
    private array $listeners = [

    ];
    private EventDispatcher $eventDispatcher;
    private UserRepositoryInterface $repository;
    private Hydrator $hydrator;
    private RequestCodeRepositoryInterface $codeRepository;

    public function __construct(
        UserRepositoryInterface        $repository,
        RequestCodeRepositoryInterface $codeRepository,
    )
    {
        $this->eventDispatcher = new EventDispatcher($this->listeners);
        $this->repository = $repository;
        $this->hydrator = new Hydrator();
        $this->codeRepository = $codeRepository;
    }

    /**
     * @throws UserNotFoundException
     * @throws Exception
     */
    public function create(CreateUserDto $dto): void
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

        $this->repository->create($user);
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
    public function updateUserName(UpdateUserNameDto $dto)
    {
        $user = $this->repository->find(new Uuid($dto->uuid));
        $user->changeUserName(new UserName($dto->firstName, $dto->lastName));
        $this->repository->update($user);
    }

    /**
     * @throws UserUnauthenticatedException
     * @throws RequestCodeThrottlingException
     * @throws EmailAlreadyRegisteredException
     */
    public function requestCodeToChangeEmail(string $email): void
    {
        $user = $this->repository->me();
        $emailVO = new \MarketPlace\Common\Domain\ValueObject\Email($email);

        if ($this->repository->existByEmail(new Email($emailVO->getEmail()))) {
            throw new EmailAlreadyRegisteredException();
        }

        try {
            $requestCode = $this->codeRepository->findByUser($user->getUuid());
            $requestCode->setRecipient($emailVO);
            $requestCode->sendSmsConfirmationCode();
            $this->codeRepository->update($requestCode);
        } catch (RequestCodeNotFoundException $e) {
            $requestCode = new RequestCode(
                uuid: Uuid::next(),
                recipient: $emailVO,
                code: ConfirmationCode::generate(),
            );
            $requestCode->setUserUuid($user->getUuid());

            $this->codeRepository->create($requestCode);
        }
    }

    /**
     * @throws ConfirmationCodeIncorrectException
     * @throws UserNotFoundException
     * @throws UserUnauthenticatedException
     * @throws RequestCodeNotFoundException
     * @throws EmailAlreadyRegisteredException
     */
    public function changeEmail(ChangeEmailDto $dto): void
    {
        $user = $this->repository->me();
        $emailVO = new \MarketPlace\Common\Domain\ValueObject\Email($dto->email);

        if ($this->repository->existByEmail(new Email($emailVO->getEmail()))) {
            throw new EmailAlreadyRegisteredException();
        }

        $requestCode = $this->codeRepository->findByUser($user->getUuid());
        if (!$requestCode->isConfirmationCodeCorrect($emailVO, new ConfirmationCode($dto->confirmationCode))) {
            throw new ConfirmationCodeIncorrectException();
        }

        $user->changeEmail(new Email($emailVO->getEmail()));
        $this->repository->update($user);
        $this->codeRepository->delete($requestCode);
    }

    /**
     * @throws UserUnauthenticatedException
     * @throws RequestCodeThrottlingException
     * @throws PhoneAlreadyRegisteredException
     */
    public function requestCodeToChangePhone(string $regionCode, string $phone): void
    {
        $user = $this->repository->me();
        $phoneVO = \MarketPlace\Common\Domain\ValueObject\Phone::fromString($regionCode, $phone);

        if ($this->repository->existByPhone(Phone::fromString($regionCode, $phone))) {
            throw new PhoneAlreadyRegisteredException();
        }

        try {
            $requestCode = $this->codeRepository->findByUser($user->getUuid());
            $requestCode->setRecipient($phoneVO);
            $requestCode->sendSmsConfirmationCode();
            $this->codeRepository->update($requestCode);
        } catch (RequestCodeNotFoundException $e) {
            $requestCode = new RequestCode(
                uuid: Uuid::next(),
                recipient: $phoneVO,
                code: ConfirmationCode::generate(),
            );
            $requestCode->setUserUuid($user->getUuid());

            $this->codeRepository->create($requestCode);
        }
    }

    /**
     * @throws ConfirmationCodeIncorrectException
     * @throws UserNotFoundException
     * @throws UserUnauthenticatedException
     * @throws RequestCodeNotFoundException
     * @throws PhoneAlreadyRegisteredException
     */
    public function changePhone(ChangePhoneDto $dto): void
    {
        $user = $this->repository->me();
        $phoneVO = \MarketPlace\Common\Domain\ValueObject\Phone::fromString($dto->regionCode, $dto->number);

        if ($this->repository->existByPhone(Phone::fromString($dto->regionCode, $dto->number))) {
            throw new PhoneAlreadyRegisteredException();
        }

        $requestCode = $this->codeRepository->findByUser($user->getUuid());
        if (!$requestCode->isConfirmationCodeCorrect($phoneVO, new ConfirmationCode($dto->confirmationCode))) {
            throw new ConfirmationCodeIncorrectException();
        }

        $user->changePhone(Phone::fromString($dto->regionCode, $dto->number));
        $this->repository->update($user);
        $this->codeRepository->delete($requestCode);
    }

    /**
     * @throws RequestCodeThrottlingException
     */
    public function requestCodeForRegister(string $getRegionCode, string $phone): void
    {
        $phoneVO = \MarketPlace\Common\Domain\ValueObject\Phone::fromString($getRegionCode, $phone);

        try {
            $requestCode = $this->codeRepository->findByPhone($phoneVO);
            $requestCode->setRecipient($phoneVO);
            $requestCode->sendSmsConfirmationCode();
            $this->codeRepository->update($requestCode);
        } catch (RequestCodeNotFoundException $e) {
            $requestCode = new RequestCode(
                uuid: Uuid::next(),
                recipient: $phoneVO,
                code: ConfirmationCode::generate(),
            );

            $this->codeRepository->create($requestCode);
        }
    }

    public function isConfirmationCodeCorrect(array $data): bool
    {
        $phoneVO = \MarketPlace\Common\Domain\ValueObject\Phone::fromString($data['regionCode'], $data['phone']);

        try {
            $requestCode = $this->codeRepository->findByPhone($phoneVO);
            return $requestCode->getCode()->isEqual(new ConfirmationCode($data['code']));
        } catch (RequestCodeNotFoundException $e) {
            return false;
        }
    }

    public function clearConfirmationCode(array $data): void
    {
        $phoneVO = \MarketPlace\Common\Domain\ValueObject\Phone::fromString($data['regionCode'], $data['phone']);
        try {
            $requestCode = $this->codeRepository->findByPhone($phoneVO);
            $this->codeRepository->delete($requestCode);
        } catch (RequestCodeNotFoundException $e) {}
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
