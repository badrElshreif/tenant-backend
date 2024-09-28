<?php

namespace App\Tenant\AppContent\Domain\Services\Page;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\AppContent\Domain\Models\Page;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class GetPageService extends Service
{
    public function handle($data = [])
    {
        try {
        	$page = Page::whereTarget('admin')->whereSlug($data['slug'])->firstOrFail();
            return new GenericPayload($page, Response::HTTP_CREATED);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                ['message' => __('error.someThingWrong')], 422
            );
        }

    }
}
