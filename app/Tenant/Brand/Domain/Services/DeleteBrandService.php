<?php

namespace App\Tenant\Brand\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Brand\Domain\Repositories\BrandRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DeleteBrandService extends Service
{

     /**
     * @var BrandRepository
     */
    private BrandRepository $brandRepository;

    /**
     * Create a new ShowBrandService instance.
     *
     * @param BrandRepository $brandRepository
     */
    public function __construct(BrandRepository $brandRepository)
    {
        $this->brandRepository = $brandRepository;
    }

    /**
     * Handle brand deletion.
     *
     * @param array $data The request data
     * @return array{status: bool, message: string}|GenericPayload
     */
    public function handle(array $data = [])
    {
        try {
            $brand = $this->brandRepository->findOrFail($data['brand_id']);
            if ($brand->products()->count() > 0)
                return new GenericPayload(
                    __('error.cannotDelete'), 422
                );
            $this->brandRepository->delete($brand->id);

            return [
                'status' => true,
                'message' => __('success.deletedSuccessfuly'),
            ];
        } catch (ModelNotFoundException $e) {
            return [
                'status' => false,
                'message' => 'Brand not found',
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => 'Failed to delete brand: ' . $e->getMessage(),
            ];
        }

    }
}
