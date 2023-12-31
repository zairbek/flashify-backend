<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Backoffice\V1\Digest\Icons;

use App\Http\Controllers\Controller;
use DB;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use MarketPlace\Backoffice\Digest\Icon\Application\Service\IconService;
use MarketPlace\Backoffice\Digest\Icon\Infrastructure\Exception\IconNotFoundException;
use MarketPlace\Common\Domain\ValueObject\Uuid;

class DeleteIconController extends Controller
{
    private IconService $service;

    public function __construct(IconService $service)
    {
        $this->service = $service;
    }

    public function __invoke(Request $request, $uuid): JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->service->deleteIcon(new Uuid($uuid));
            DB::commit();
            return response()->json(['message' => 'ok'], 204);
        } catch (IconNotFoundException $e) {
            DB::rollBack();
            return response()->json(['message' => 'not found'], 404);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
