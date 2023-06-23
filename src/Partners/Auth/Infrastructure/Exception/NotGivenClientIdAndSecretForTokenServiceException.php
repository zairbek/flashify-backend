<?php

declare(strict_types=1);

namespace MarketPlace\Partners\Auth\Infrastructure\Exception;

use Exception;

class NotGivenClientIdAndSecretForTokenServiceException extends Exception
{
    protected $message = 'Не передан клиент id и секретный ключ для генерации токена';
}
