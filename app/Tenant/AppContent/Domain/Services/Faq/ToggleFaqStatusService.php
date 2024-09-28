<?php

namespace App\Tenant\AppContent\Domain\Services\API;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\AppContent\Domain\Models\Faq;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use DB;

class ToggleFaqStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            // Begin Transaction
            DB::beginTransaction();
            $faq = Faq::findOrFail($data['faq_id']);
            $faq->update([
                'is_active' => !$faq->is_active
            ]);

            DB::commit();
            return new GenericPayload($faq);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            // Rollback Transaction
            DB::rollback();
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            // Rollback Transaction
            DB::rollback();
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }


    }
}
