<?php

namespace App\Tenant\Brand\Controllers;

use App\Infrastructure\Http\Controllers\Controller;
use App\Tenant\Brand\Domain\Models\Brand;
use App\Tenant\Brand\Domain\Resources\BrandLiteResource;
use App\Tenant\Brand\Domain\Resources\BrandResource;
use App\Tenant\Brand\Domain\Services\ListBrandsService;
use App\Tenant\Brand\Domain\Repositories\BrandRepository;
use App\Tenant\Brand\Domain\Requests\BrandRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Inertia\Inertia;

class BrandController extends Controller
{

    /**
     * @var BrandRepository
     */
    private BrandRepository $brandRepository;

    /**
     * Create a new BrandController instance.
     *
     * @param BrandRepository $brandRepository
     */
    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

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

    /**
     * Store a newly created brand in storage.
     *
     * @param BrandRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(BrandRequest $request)
    {
        try {
            // Get validated data from the request
            $data = $request->validated();

            // Handle file upload if present
            if ($request->hasFile('logo')) {
                $data['image'] = $request->file('logo');
            }

            // Convert status to is_active if needed
            if (isset($data['status'])) {
                $data['is_active'] = $data['status'];
                unset($data['status']);
            }

            // Process translations data
            if (isset($data['translations'])) {
                foreach ($data['translations'] as $locale => $translation) {
                    $data[$locale] = $translation;
                }
                unset($data['translations']);
            }

            // Create brand using repository
            $brand = $this->brandRepository->create($data);

            return redirect(routeTenant('tenant.dashboard.brands.index'))
                ->with('success', 'Brand created successfully');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to create brand: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for editing the specified brand.
     *
     * @param int $id
     * @return \Inertia\Response
     */
    public function edit($id)
    {
        try {
            $brand = $this->brandRepository->find($id);

            if (!$brand) {
                return back()->withErrors(['error' => 'Brand not found']);
            }

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'brand' => new BrandResource($brand)
                ]);
            }

            return Inertia('Brands/Edit', [
                'brand' => new BrandResource($brand)
            ]);

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch brand: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Failed to fetch brand: ' . $e->getMessage()]);
        }
    }

    /**
     * Update the specified brand in storage.
     *
     * @param BrandRequest $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function update(BrandRequest $request, $id)
    {
        try {
            // Get validated data from the request
            $data = $request->validated();
            // Handle file upload if present
            if ($request->hasFile('logo')) {
                $data['image'] = $request->file('logo');
            }

            // Convert status to is_active if needed
            if (isset($data['status'])) {
                $data['is_active'] = $data['status'];
                unset($data['status']);
            }

            // Get the brand
            $brand = $this->brandRepository->find($id);
            if (!$brand) {
                throw new \Exception('Brand not found');
            }

            // Process translations data
           // Process translations data
           if (isset($data['translations'])) {
            foreach ($data['translations'] as $locale => $translation) {
                $data[$locale] = $translation;
            }
            unset($data['translations']);
        }


            // Update brand using repository
            $brand = $this->brandRepository->update($id, $data);

            if (request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Brand updated successfully',
                    'brand' => new BrandResource($brand)
                ]);
            }

            return redirect(routeTenant('tenant.dashboard.brands.index'))
                ->with('success', 'Brand updated successfully');

        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update brand: ' . $e->getMessage()
                ], 500);
            }
            return back()->withErrors(['error' => 'Failed to update brand: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete a brand
     *
     * @param int $id Brand ID to delete
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        try {
            // Attempt to delete the brand
            $deleted = $this->brandRepository->destroy($id);

            if ($deleted) {
                return redirect(routeTenant('tenant.dashboard.brands.index'))
                    ->with('success', 'Brand deleted successfully');
            } else {
                return back()->withErrors(['error' => 'Failed to delete brand']);
            }
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete brand: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle the active status of a brand
     *
     * @param int $id Brand ID to toggle status
     * @return \Illuminate\Http\Response
     */
    public function toggleStatus($id)
    {
        try {
            // Attempt to toggle the brand status
            $brand = $this->brandRepository->toggleStatus($id);

            if ($brand) {
                $statusText = $brand->is_active ? 'activated' : 'deactivated';

                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => "Brand {$statusText} successfully",
                        'brand' => new \App\Tenant\Brand\Domain\Resources\BrandResource($brand)
                    ]);
                }

                return redirect()->back()->with('success', "Brand {$statusText} successfully");
            } else {
                if (request()->wantsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Failed to update brand status'
                    ], 422);
                }

                return redirect()->back()->withErrors(['error' => 'Failed to update brand status']);
            }
        } catch (\Exception $e) {
            if (request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update brand status: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Failed to update brand status: ' . $e->getMessage()]);
        }
    }
}
