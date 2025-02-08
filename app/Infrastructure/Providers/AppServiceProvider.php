<?php

namespace App\Infrastructure\Providers;

use App\Infrastructure\Models\Passport\PassportAuthCode;
use App\Infrastructure\Models\Passport\PassportClient;
use App\Infrastructure\Models\Passport\PassportRefreshToken;
use App\Infrastructure\Models\Passport\PassportToken;
use App\Infrastructure\Models\Passport\PersonalAccessClient;
use App\Main\Tenant\Domain\Models\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

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

        if (getDomain() != env('APP_DOMAIN')) {
            $tenant = Tenant::where('domain', request()->getHost())->firstOrFail();
        } elseif (!empty(getSubdomain())) {
            $tenant = getSubdomain() ?? "";
            $tenant = Tenant::where('slug', $tenant)->firstOrFail();
        }


        if (!empty($tenant)) {
            //dd($tenant);
            //establish connection based on tenant (tenant_id)
            $database = "tenant_{$tenant->id}";

            config(["database.connections.tenant.database" => $database]);
            config(['database.default' => 'tenant']);
            config(["passport.connection" => 'tenant']);

            config(["telescope.storage.database.connection" => "tenant"]);

            DB::purge('tenant');
            DB::reconnect('tenant');

            app()->instance(Tenant::class, $tenant);
        }

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
            'tenant-user' => 'Customer Store Type',
            'tenant-vendor' => 'Vendor Store Type',
        ]);


        Gate::before(function ($user, $ability) {
            return $user->is_super_admin;
        });

        Factory::guessFactoryNamesUsing(function (string $model_name) {
            $namespace = 'Database\\Factories\\';
            $model_name = Str::afterLast($model_name, '\\');
            return $namespace . $model_name . 'Factory';
        });

        // Specify the main folder you want to migrate from
        $migrationsPath = database_path('migrations/tenant');

        if (File::exists($migrationsPath)) {
            // Recursively get all migration files from the specific folder and its subfolders
            $migrationFiles = File::allFiles($migrationsPath);
            $migrationPaths = [];

            // Convert SplFileInfo objects to the full pathnames
            foreach ($migrationFiles as $file) {
                $migrationPaths[] = $file->getPathname();
            }

            // Tell Laravel to load migrations from these paths
            $this->loadMigrationsFrom($migrationPaths);
        }

    }
}
