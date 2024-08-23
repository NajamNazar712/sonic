<?php

namespace App\Observers;

use App\Http\Models\Admin\Admin;
use Auth;
use Log;
class AdminUserObserver
{
    /**
     * Handle the admin "created" event.
     *
     * @param  \App\Admin  $admin
     * @return void
     */
    public function created(Admin $admin)
    {
        //
    }

    /**
     * Handle the admin "updated" event.
     *
     * @param  \App\Admin  $admin
     * @return void
     */
    public function updated(Admin $admin)
    {
        //
    }

    /**
     * Handle the admin "deleted" event.
     *
     * @param  \App\Admin  $admin
     * @return void
     */
    public function deleted(Admin $admin)
    {
        //
    }

    /**
     * Handle the admin "restored" event.
     *
     * @param  \App\Admin  $admin
     * @return void
     */
    public function restored(Admin $admin)
    {
        //
    }

    /**
     * Handle the admin "force deleted" event.
     *
     * @param  \App\Admin  $admin
     * @return void
     */
    public function forceDeleted(Admin $admin)
    {
        //
    }

    public function saved(Admin $admin)
    {
        $dirtyAttributes = $admin->getDirty();

        // Iterate over each dirty attribute
        foreach ($dirtyAttributes as $attribute => $newValue) {
            // Get the original value of the attribute
            $originalValue = $admin->getOriginal($attribute);

            // Log the change
            Log::info("The attribute '$attribute' is changing from '$originalValue' to '$newValue'");
        }
    }
}   
