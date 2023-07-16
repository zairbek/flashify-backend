<?php

declare(strict_types=1);

namespace MarketPlace\Partners\User\Infrastructure\Repository;

use App\Models\RequestCode as RequestCodeDB;
use MarketPlace\Common\Domain\ValueObject\ConfirmationCode;
use MarketPlace\Common\Domain\ValueObject\Email;
use MarketPlace\Common\Domain\ValueObject\Phone;
use MarketPlace\Common\Domain\ValueObject\SendAt;
use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Common\Infrastructure\Service\Hydrator;
use MarketPlace\Partners\User\Domain\Entity\RequestCode;
use MarketPlace\Partners\User\Domain\Repository\RequestCodeRepositoryInterface;
use MarketPlace\Partners\User\Infrastructure\Exception\RequestCodeNotFoundException;

class RequestCodeRepository implements RequestCodeRepositoryInterface
{
    private Hydrator $hydrator;

    public function __construct()
    {
        $this->hydrator = new Hydrator();
    }

    public function create(RequestCode $requestCode): void
    {
        $recipient = $requestCode->getRecipient();

        $email = $recipient instanceof Email ? $recipient->getEmail() : null;
        $phone = $recipient instanceof Phone ? [
            'regionCode' => $recipient->getRegionCode(),
            'number' => $recipient->toString()
        ]  : null;

        RequestCodeDB::create([
            'uuid' => $requestCode->getUuid()->getId(),
            'account_uuid' => $requestCode->getUserUuid()?->getId(),
            'email' => $email,
            'phone' => $phone,
            'code' => $requestCode->getCode()->getCode(),
            'sendAt' => $requestCode->getSendAt()->getDateTime()
        ]);
    }

    /**
     * @throws RequestCodeNotFoundException
     */
    public function update(RequestCode $requestCode): void
    {
        /** @var RequestCodeDB|null $requestCodeDB */
        $requestCodeDB = RequestCodeDB::firstWhere('uuid', $requestCode->getUuid()->getId());

        if (is_null($requestCodeDB)) {
            throw new RequestCodeNotFoundException();
        }

        $recipient = $requestCode->getRecipient();
        $email = $recipient instanceof Email ? $recipient->getEmail() : null;
        $phone = $recipient instanceof Phone ? [
            'regionCode' => $recipient->getRegionCode(),
            'number' => $recipient->toString()
        ]  : null;

        $requestCodeDB->update([
            'uuid' => $requestCode->getUuid()->getId(),
            'account_uuid' => $requestCode->getUserUuid()?->getId(),
            'email' => $email,
            'phone' => $phone,
            'code' => $requestCode->getCode()->getCode(),
            'sendAt' => $requestCode->getSendAt()->getDateTime()
        ]);
    }

    public function delete(RequestCode $requestCode): void
    {
        RequestCodeDB::where('uuid', $requestCode->getUuid()->getId())->delete();
    }

    /**
     * @inheritDoc
     */
    public function find(Uuid $uuid): RequestCode
    {
        /** @var RequestCodeDB $requestCode */
        $requestCode = RequestCodeDB::query()->firstWhere('uuid', $uuid->getId());

        if (is_null($requestCode)) {
            throw new RequestCodeNotFoundException();
        }

        return $this->hydrate($requestCode);
    }

    /**
     * @inheritDoc
     */
    public function findByUser(Uuid $userUuid): RequestCode
    {
        /** @var RequestCodeDB|null $requestCode */
        $requestCode = RequestCodeDB::query()->firstWhere('account_uuid', $userUuid->getId());

        if (is_null($requestCode)) {
            throw new RequestCodeNotFoundException();
        }

        return $this->hydrate($requestCode);
    }

    /**
     * @inheritDoc
     */
    public function findByPhone(Phone $phone): RequestCode
    {
        $requestCode = RequestCodeDB::whereJsonContains('phone', [
            'regionCode' => $phone->getRegionCode(),
            'number' => $phone->toString()]
        )->first();

        if (is_null($requestCode)) {
            throw new RequestCodeNotFoundException();
        }

        return $this->hydrate($requestCode);
    }

    /**
     * @inheritDoc
     */
    public function findByEmail(Email $email): RequestCode
    {
        // TODO: Implement findByEmail() method.
    }

    private function hydrate(RequestCodeDB $requestCode): RequestCode
    {
        if ($requestCode->email) {
            $recipient = new Email($requestCode->email);
        } else {
            $recipient = Phone::fromString($requestCode->phone['regionCode'], $requestCode->phone['number']);
        }

        return $this->hydrator->hydrate(RequestCode::class, [
            'uuid' => new Uuid($requestCode->uuid),
            'userUuid' => $requestCode->account_uuid ? new Uuid($requestCode->account_uuid) : null,
            'recipient' => $recipient,
            'code' => new ConfirmationCode($requestCode->code),
            'sendAt' => new SendAt($requestCode->sendAt),
        ]);
    }
}
