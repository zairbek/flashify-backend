<?php

namespace MarketPlace\Backoffice\Auth\Domain\Repository;


use MarketPlace\Backoffice\Auth\Domain\ValueObject\JwtTokenId;

interface TokenRepositoryInterface
{

    public function signOut(JwtTokenId $jwtTokenId): void;
}
