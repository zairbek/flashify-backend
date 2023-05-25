<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Backoffice\V1\Digest\Icons;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\Digest\Icon\CreateIconRequest;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use MarketPlace\Backoffice\Digest\Icon\Application\Dto\CreateIconDto;
use MarketPlace\Backoffice\Digest\Icon\Application\Service\IconService;
use MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject\IconFile;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconAlreadyExistsException;

class CreateIconController extends Controller
{

    private IconService $service;

    public function __construct(IconService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws \Throwable
     * @throws ValidationException
     */
    public function __invoke(CreateIconRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->service->createIcon(new CreateIconDto(
                name: $request->get('name'),
                file: IconFile::fromUploadedFile($request->file('file')),
            ));
            DB::commit();
            return response()->json(['message' => 'created'], 201);
        } catch (IconAlreadyExistsException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['name' => ['С таким названиям уже существует']]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
