<?php

declare(strict_types=1);

namespace MarketPlace\Market\Auth\Infrastructure\Exception;

class NotGivenClientIdAndSecretForTokenServiceException extends \LogicException
{
    protected $message = 'Не передан клиент id и секретный ключ для генерации токена';
}
