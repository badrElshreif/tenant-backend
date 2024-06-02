<?php

namespace App\Infrastructure\Providers;

use App\Infrastructure\Models\Passport\PassportAuthCode;
use App\Infrastructure\Models\Passport\PassportClient;
use App\Infrastructure\Models\Passport\PassportRefreshToken;
use App\Infrastructure\Models\Passport\PassportToken;
use App\Infrastructure\Models\Passport\PersonalAccessClient;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

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
    public function boot(): void
    {

        Passport::useTokenModel(PassportToken::class);
        Passport::useRefreshTokenModel(PassportRefreshToken::class);
        Passport::useAuthCodeModel(PassportAuthCode::class);
        Passport::useClientModel(PassportClient::class);
        Passport::usePersonalAccessClientModel(PersonalAccessClient::class);


        Passport::tokensExpireIn(now()->addDays(15));
        Passport::refreshTokensExpireIn(now()->addDays(30));
        Passport::personalAccessTokensExpireIn(now()->addMonths(6));

        Passport::tokensCan([
            'tenant-admin' => 'Admin Store Type',
            'tenant-user' => 'User Store Type',
            'tenant-vendor' => 'Vendor Store Type',
        ]);


        Gate::before(function ($user, $ability) {
            return $user->is_super_admin;
        });

    }
}
