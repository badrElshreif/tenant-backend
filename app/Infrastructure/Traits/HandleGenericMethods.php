<?php

namespace App\Infrastructure\Traits;

use Exception;
use App\Order\Domain\Models\Status;

use App\Infrastructure\Exceptions\NotFoundException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

trait HandleGenericMethods
{

    public function setting($key = null)
    {
        if (is_null($key)) {
            return optional(\App\Tenant\AppContent\Domain\Models\Setting::where('key', 'added_tax')->first())->body ?? null;
        }
        return optional(\App\Tenant\AppContent\Domain\Models\Setting::where('key', $key)->first())->body ?? null;
    }

    function status($type, $key)
    {
      return optional(\App\Order\Domain\Models\Status::where('type', $type)->where('key', $key)->first())->id ?? null;
    }

    public function getBulkStatus($keys)
    {
        $status = Status::whereIn('key', $keys)->get();

        return $status;
    }

    public function getSingleStatus($key)
    {
        try {

            return Status::where('key', $key)->firstOrFail();

        } catch(ModelNotFoundException $e) {

            throw new NotFoundException();
        }
    }

}
