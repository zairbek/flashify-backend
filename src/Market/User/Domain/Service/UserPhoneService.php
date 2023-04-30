<?php

declare(strict_types=1);

namespace MarketPlace\Market\User\Domain\Service;

use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\CreatedAt;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Hydrator;
use MarketPlace\Market\User\Application\Dto\FindUserPhoneDto;
use MarketPlace\Market\User\Application\Dto\UpdateUserPhoneDto;
use MarketPlace\Market\User\Domain\Entity\UserPhoneNumber;
use MarketPlace\Market\User\Domain\Repository\UserPhoneRepositoryInterface;
use MarketPlace\Market\User\Domain\ValueObject\UserId;
use MarketPlace\Market\User\Infrastructure\Exception\UserPhoneNumberNotFoundException;

class UserPhoneService
{
    private UserPhoneRepositoryInterface $userPhoneRepository;
    private Hydrator $hydrator;

    public function __construct(UserPhoneRepositoryInterface $userPhoneRepository)
    {
        $this->userPhoneRepository = $userPhoneRepository;
        $this->hydrator = new Hydrator();
    }

    /**
     * @param FindUserPhoneDto $dto
     * @return array
     * @throws UserPhoneNumberNotFoundException
     */
    public function findUserPhone(FindUserPhoneDto $dto): array
    {
        $userPhone = $this->userPhoneRepository->findUserPhone(phone: Phone::fromString($dto->regionIsoCode, $dto->phoneNumber));

        return [
            'uuid' => $userPhone->getUuid()->getId(),
            'phone' => [
                'regionIsoCode' => $userPhone->getPhone()->getRegionCode(),
                'number' => $userPhone->getPhone()->toString()
            ],
            'createdAt' => $userPhone->getCreatedAt()->toIsoFormat(),
            'userId' => $userPhone->getUserId()?->getUserId(),
            'confirmationCode' => $userPhone->getConfirmationCode()?->getCode(),
            'sendAt' => $userPhone->getSendAt()?->toIsoFormat(),
        ];
    }

    public function updateUserPhone(UpdateUserPhoneDto $dto): void
    {
        $userPhoneNumber = $this->hydrator->hydrate(UserPhoneNumber::class, [
            'uuid' => new Uuid($dto->uuid),
            'phone' => Phone::fromString($dto->regionIsoCode, $dto->phoneNumber),
            'createdAt' => CreatedAt::fromIsoFormat($dto->createdAt),
            'userId' => $dto->userId ? new UserId(new Uuid($dto->userId)) : null,
            'confirmationCode' => $dto->confirmationCode ? new ConfirmationCode($dto->confirmationCode) : null,
            'sendAt' => $dto->sendAt ? SendAt::fromIsoFormat($dto->sendAt) : null
        ]);

        $this->userPhoneRepository->update($userPhoneNumber);
    }
}
