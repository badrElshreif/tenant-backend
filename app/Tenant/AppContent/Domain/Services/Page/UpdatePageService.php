<?php

namespace App\Tenant\AppContent\Domain\Services\Page;

use App\Infrastructure\Domain\Payloads\GenericPayload;
use App\Infrastructure\Domain\Services\Service;
use App\Tenant\AppContent\Domain\Models\Page;
use App\Infrastructure\Exceptions\ModelNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class UpdatePageService extends Service
{
    public function handle($data = [])
    {
        try {

            $page = Page::whereSlug($data['slug'])->first();
            $page->update($data);
            return new GenericPayload($page, Response::HTTP_CREATED);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException;
        } catch (Exception $ex) {
            return new GenericPayload(
                 __('error.someThingWrong'), 422
            );
        }

    }
}
