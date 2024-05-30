<?php

namespace App\Infrastructure\Providers;

use App\Main\Tenant\Domain\Events\TenantCreated;
use App\Main\Tenant\Domain\Listeners\TenantCreateDatabase;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;


class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(
            TenantCreated::class,
            TenantCreateDatabase::class,
        );
    }

}
