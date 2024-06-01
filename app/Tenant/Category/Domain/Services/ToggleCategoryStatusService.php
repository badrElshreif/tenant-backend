<?php

namespace App\Tenant\Category\Domain\Services;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\Category\Domain\Models\Category;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class ToggleCategoryStatusService extends Service
{
    public function handle($data = [])
    {
        try {
            $category = Category::findOrFail($data['category_id']);
            if($category->is_active){
                if($category->parent_id != null || $category->type == 'centers'){
                    if(count($category->products()->active(1)->get()) > 0)
                        return new GenericPayload(
                             __('error.cannotDeactivate'), 422
                        );
                }else{
                    if(count($category->childs()->active(1)->get()) > 0)
                        return new GenericPayload(
                             __('error.cannotDeactivate'), 422
                        );
                }
            }
            $category->update([
                'is_active' => !$category->is_active
            ]);
            return new GenericPayload($category, Response::HTTP_CREATED);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                __('error.someThingWrong'), 422
            );
        }
    }
}
