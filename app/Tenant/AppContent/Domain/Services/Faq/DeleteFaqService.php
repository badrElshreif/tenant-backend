<?php

namespace App\Tenant\AppContent\Domain\Services\API;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\AppContent\Domain\Models\Faq;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use App\Infrastructure\Exceptions\QueryException;

class DeleteFaqService extends Service
{
    public function handle($data = [])
    {
        try {
            $faq = Faq::findOrFail($data['faq_id']);
            $faq->delete();
            return new GenericPayload(['message' => __('success.deletedSuccessfuly')]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (\Exception $ex) {
            throw new QueryException;
        }

    }
}
