<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Market\V1\Auth\Phone;

use App\Http\Controllers\Controller;
use App\Http\Requests\Market\Auth\Phone\RequestCodeToPhoneNumberRequest;
use Illuminate\Http\Response;

class RequestCodeToPhoneNumberController extends Controller
{
    public function __invoke(RequestCodeToPhoneNumberRequest $request, Response $response): Response
    {

        dd($request);

        return $response;
    }
}
