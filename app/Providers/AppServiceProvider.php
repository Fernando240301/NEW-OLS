<?php

namespace App\Providers;
use Illuminate\Support\Facades\Gate;
use App\Models\PPJB;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
    // Jika kamu punya policies, bisa register di sini juga
    // $this->registerPolicies();

    Gate::define('approve-ppjb', function ($user, PPJB $ppjb) {
        $approval = $ppjb->currentApproval();

        if (!$approval) {
            return false;
        }

        return $approval->user_id == $user->userid;
    });
    }

}
