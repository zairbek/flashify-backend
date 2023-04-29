<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Market\V1;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;

class DocumentationController extends Controller
{
    public function ui(): \Illuminate\Contracts\Foundation\Application|Factory|View|Application
    {
        return view('api.v1.market.documentation');
    }

    public function file()
    {
        $docs = storage_path('documentation/market.yaml');
        return response()->file($docs);
    }
}
