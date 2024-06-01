<?php

namespace App\Tenant\Product\Domain\Services\Rating;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Product\Domain\Resources\RatingResource;
use App\Tenant\Product\Domain\Models\Rating;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class UpdateCommentStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            $rating = Rating::findOrFail($data['rating_id']);
            $rating->update([
                'is_active' => !$rating->is_active
            ]);
           //return new GenericPayload(['message' =>  __('success.updatedSuccessfuly')]);
           return new GenericPayload($rating, Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (\PDOException $ex){
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
