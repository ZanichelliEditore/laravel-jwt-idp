<?php

namespace App\Providers;

use App\Repositories\RoleRepository;
use App\Repositories\UserRepository;
use App\Repositories\ClientRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\ProviderRepository;
use App\Repositories\RepositoryInterface;
use App\Listeners\LogoutProvidersListener;
use App\Repositories\OauthClientsRepository;
use App\Repositories\UserRepositoryInterface;
use App\Http\Controllers\Manage\RoleController;
use App\Http\Controllers\Manage\UserController;
use App\Repositories\ClientRepositoryInterface;
use App\Repositories\VerificationTokenRepository;
use App\Http\Controllers\Manage\ProviderController;
use App\Http\Controllers\Manage\OauthClientsController;
use App\Http\Controllers\JwtAuth\VerificationController;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->when(LogoutProvidersListener::class)
            ->needs(RepositoryInterface::class)
            ->give(ProviderRepository::class);

        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        $this->app->when(ProviderController::class)
            ->needs(RepositoryInterface::class)
            ->give(ProviderRepository::class);

        $this->app->when(RoleController::class)
            ->needs(RepositoryInterface::class)
            ->give(RoleRepository::class);

        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);

        $this->app->when(UserController::class)
            ->needs(RepositoryInterface::class)
            ->give(VerificationTokenRepository::class);

        $this->app->when(VerificationController::class)
            ->needs(RepositoryInterface::class)
            ->give(VerificationTokenRepository::class);

        $this->app->when(OauthClientsController::class)
            ->needs(OauthClientsRepository::class)
            ->give(OauthClientsRepository::class);
    }
}
