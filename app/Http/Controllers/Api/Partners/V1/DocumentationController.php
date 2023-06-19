<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Partners\V1;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentationController extends Controller
{
    public function ui(): \Illuminate\Contracts\Foundation\Application|Factory|View|Application
    {
        return view('api.v1.partners.documentation');
    }

    public function file(): BinaryFileResponse
    {
        $docs = storage_path('documentation/partners.yaml');
        return response()->file($docs);
    }
}
