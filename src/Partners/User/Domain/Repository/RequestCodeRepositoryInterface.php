<?php

namespace MarketPlace\Partners\User\Domain\Repository;

use MarketPlace\Common\Domain\ValueObject\Uuid;
use MarketPlace\Partners\User\Domain\Entity\RequestCode;
use MarketPlace\Partners\User\Domain\ValueObject\Email;
use MarketPlace\Partners\User\Domain\ValueObject\Phone;
use MarketPlace\Partners\User\Infrastructure\Exception\RequestCodeNotFoundException;

interface RequestCodeRepositoryInterface
{
    public function create(RequestCode $requestCode): void;

    public function update(RequestCode $requestCode): void;

    public function delete(RequestCode $requestCode): void;

    /**
     * @throws RequestCodeNotFoundException
     */
    public function find(Uuid $uuid): RequestCode;

    /**
     * @throws RequestCodeNotFoundException
     */
    public function findByUser(Uuid $userUuid): RequestCode;

    /**
     * @throws RequestCodeNotFoundException
     */
    public function findByPhone(Phone $phone): RequestCode;

    /**
     * @throws RequestCodeNotFoundException
     */
    public function findByEmail(Email $email): RequestCode;
}
