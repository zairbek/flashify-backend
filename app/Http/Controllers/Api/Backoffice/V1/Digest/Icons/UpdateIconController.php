<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Backoffice\V1\Digest\Icons;

use App\Http\Controllers\Controller;
use App\Http\Requests\Backoffice\Digest\Icon\UpdateIconRequest;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use MarketPlace\Backoffice\Digest\Icon\Application\Dto\UpdateIconDto;
use MarketPlace\Backoffice\Digest\Icon\Application\Service\IconService;
use MarketPlace\Backoffice\Digest\Icon\Domain\ValueObject\IconFile;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconAlreadyExistsException;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconNotFoundException;

class UpdateIconController extends Controller
{
    private IconService $service;

    public function __construct(IconService $service)
    {
        $this->service = $service;
    }

    /**
     * @throws ValidationException
     */
    public function __invoke(UpdateIconRequest $request, $uuid): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->service->updateIcon(new UpdateIconDto(
                uuid: $uuid,
                name: $request->get('name'),
                file: $request->file('file') ? IconFile::fromUploadedFile($request->file('file')) : null
            ));
            DB::commit();
            return response()->json(['message' => 'ok']);
        } catch (IconAlreadyExistsException $e) {
            DB::rollBack();
            throw ValidationException::withMessages(['name' => ['Имя с таким именем уже существует в базе']]);
        } catch (IconNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'not found'], 404);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
