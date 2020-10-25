<?php

namespace SpiritSystems\DayByDay\Core\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use SpiritSystems\DayByDay\Core\Http\Controllers\IntegrationsController;
use SpiritSystems\DayByDay\Core\Http\Controllers\LeadsController;
use SpiritSystems\DayByDay\Core\Http\Controllers\PagesController;
use SpiritSystems\DayByDay\Core\Http\Livewire\OutcomeByPeriod;
use SpiritSystems\DayByDay\Core\Pipes\MenuProviderPipe;
use SpiritSystems\DayByDay\Core\Services\MenuService;
use SpiritSystems\DayByDay\Core\Services\Storage\GetStorageProvider;
use SpiritSystems\DayByDay\Core\Services\Storage\Local;
use SpiritSystems\DayByDay\Core\View\DayByDayViewFinder;

class DayByDayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        MenuService::addProvider(MenuProviderPipe::class);
        $this->app->bind('view.finder', function ($app) {
            return new DayByDayViewFinder($app['files'], $app['config']['view.paths']);
        });
        $this->app->bind(\App\Http\Controllers\LeadsController::class, LeadsController::class);
        $this->app->bind(\App\Http\Controllers\IntegrationsController::class, IntegrationsController::class);
        $this->app->bind(\App\Http\Controllers\PagesController::class, PagesController::class);
        $this->app->bind(\App\Http\Requests\Lead\StoreLeadRequest::class, \SpiritSystems\DayByDay\Core\Http\Requests\StoreLeadRequest::class);

        Livewire::component('outcome-by-period', OutcomeByPeriod::class);

        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/web.php');
    }
}
