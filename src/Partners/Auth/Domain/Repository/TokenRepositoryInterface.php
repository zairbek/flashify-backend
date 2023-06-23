<?php

namespace MarketPlace\Partners\Auth\Domain\Repository;

use MarketPlace\Partners\Auth\Domain\ValueObject\JwtTokenId;

interface TokenRepositoryInterface
{

    public function signOut(JwtTokenId $jwtTokenId): void;
}
