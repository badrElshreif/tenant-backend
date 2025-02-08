<?php

namespace App\User\Domain\Observers;

use App\User\Domain\Models\Customer;

class UserObserver
{
    /**
     * Handle the user "created" event.
     *
     * @param  \App\User\Domain\Models\Customer  $user
     * @return void
     */
    public function created(Customer $user)
    {
        //
    }

    /**
     * Handle the user "updated" event.
     *
     * @param  \App\User\Domain\Models\Customer  $user
     * @return void
     */
    public function updated(Customer $user)
    {
        //
    }

    /**
     * Handle the user "deleted" event.
     *
     * @param  \App\User\Domain\Models\Customer  $user
     * @return void
     */
    public function deleted(Customer $user)
    {
        //
    }

    /**
     * Handle the user "restored" event.
     *
     * @param  \App\User\Domain\Models\Customer  $user
     * @return void
     */
    public function restored(Customer $user)
    {
        //
    }

    /**
     * Handle the user "force deleted" event.
     *
     * @param  \App\User\Domain\Models\Customer  $user
     * @return void
     */
    public function forceDeleted(Customer $user)
    {
        //
    }
}
