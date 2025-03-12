<?php

namespace App\Tenant\Brand\Controllers;


use App\Infrastructure\Http\Controllers\Controller;
use App\Tenant\Brand\Domain\Models\Brand;
use App\Tenant\Brand\Domain\Resources\BrandLiteResource;
use App\Tenant\Brand\Domain\Services\ListBrandsService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BrandController extends Controller
{

    public function index(Request $request, ListBrandsService $listBrandsService)
    {

        $request->merge([
            'is_paginated' => 1,
        ]);

        $brands = $listBrandsService->handle($request->all());
       // return $brands;
        return Inertia('Brands/Index', [
            'brands' => $brands['data'],
        ]);

    }

    public function create()
    {

        return Inertia('Brands/Create', [
            'brands' => []//$brands['data'],
        ]);
    }

}
