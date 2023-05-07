<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Backoffice\V1;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentationController extends Controller
{
    public function ui(): \Illuminate\Contracts\Foundation\Application|Factory|View|Application
    {
        return view('api.v1.backoffice.documentation');
    }

    public function file(): BinaryFileResponse
    {
        $docs = storage_path('documentation/backoffice.yaml');
        return response()->file($docs);
    }
}
