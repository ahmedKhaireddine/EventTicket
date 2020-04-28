<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Gate::define('to-speak', function ($user, $user_id) {
            return $user->id != $user_id;
        });

        Gate::define('create-admin', function ($user) {
            return $user->is_super_admin == true;
        });

        Gate::define('active-event', function ($user) {
            return $user->isAdmin() || $user->is_super_admin == true;
        });
    }
}
