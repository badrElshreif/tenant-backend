<?php

namespace App\Tenant\Location\Domain\Services;

use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Location\Domain\Repositories\StateRepository;
use Symfony\Component\HttpFoundation\Response;

class DeleteStateService extends Service
{

    protected $stateRepository;

    public function __construct(StateRepository $stateRepository)
    {
        $this->stateRepository = $stateRepository;
    }

    public function handle($data = [])
    {
        try {
            $state = $this->stateRepository->findOrFail($data['state_id']);
            if ($state->cities()->where('is_active', 1)->count() > 0)
                return [
                    'status' => false,
                    'message' => __('error.cannotDelete'),
                    'code' => Response::HTTP_NO_CONTENT,
                ];

//            if($state->addresses()->count() > 0)
//                return new GenericPayload(
//                    __('error.cannotDelete'), 422
//                );

            $state->delete();
            return [
                'status' => true,
                'message' => __('success.deletedSuccessfuly'),
                'code' => Response::HTTP_NO_CONTENT,
            ];
        } catch (\Exception $ex) {
            return [
                'status' => false,
                'message' => $ex->getMessage(),
                'code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ];
        }
    }
}
