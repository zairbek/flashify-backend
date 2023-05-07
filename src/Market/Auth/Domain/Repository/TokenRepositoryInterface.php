<?php

namespace MarketPlace\Market\Auth\Domain\Repository;

use MarketPlace\Market\Auth\Domain\ValueObject\JwtTokenId;

interface TokenRepositoryInterface
{

    public function signOut(JwtTokenId $jwtTokenId): void;
}
