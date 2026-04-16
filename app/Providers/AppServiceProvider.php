<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use App\View\Composers\AdminComposer;
use App\View\Composers\AgentComposer;
use App\View\Composers\GlobalComposer;
use App\View\Composers\TenantComposer;
use Illuminate\Support\ServiceProvider;
use App\View\Composers\FrontendComposer;
use App\View\Composers\TenantFrontLangBlade;
use App\View\Composers\TenantFrontendComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        if (!app()->runningInConsole()) {

            $this->composeSpecificViews();
        }
    }

    /**
     * Compose specific views.
     *
     * @return void
     */
    protected function composeSpecificViews()
    {
        View::composer('*', GlobalComposer::class);
        View::composer('admin.*', AdminComposer::class);
        View::composer('front.*', FrontendComposer::class);
        View::composer('user.*', TenantComposer::class);
        // this is  for where use TenantFontentLanguage
        View::composer([
            'user.partials.languages',
            'user.partials.side-navbar',
            'agent.partials.side-navbar',
            'agent.partials.languages',
        ], TenantFrontLangBlade::class);
        View::composer('agent.*', AgentComposer::class);
        View::composer(['tenant_frontend.*', 'components.tenant.frontend.*'], TenantFrontendComposer::class);
    }
}
