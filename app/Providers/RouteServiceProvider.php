<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';
    protected $moduleNamespace = 'Modules\Shops\Http\Controllers';
    protected $restaurantmoduleNamespace = 'Modules\Restaurant\Http\Controllers';
    protected $accountsmoduleNamespace = 'Modules\Accounts\Http\Controllers';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));


            /*----------------Hotel module ---------------------*/

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->moduleNamespace)
                ->group(module_path('Shops', '/Routes/api.php'));

            Route::middleware('web')
                ->namespace($this->moduleNamespace)
                ->group(module_path('Shops', '/Routes/web.php'));

            /*--------------------- Restaurant module -------------*/

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->restaurantmoduleNamespace)
                ->group(module_path('Restaurant', '/Routes/api.php'));

            Route::middleware('web')
                ->namespace($this->restaurantmoduleNamespace)
                ->group(module_path('Restaurant', '/Routes/web.php'));

            /*---------------------- Account module ------------------------*/

            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->accountsmoduleNamespace)
                ->group(module_path('Accounts', '/Routes/api.php'));

            Route::middleware('web')
                ->namespace($this->accountsmoduleNamespace)
                ->group(module_path('Accounts', '/Routes/web.php'));

            /*-------------------- module ends --------------------------*/

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            Route::middleware('spa')
                ->namespace($this->namespace)
                ->group(base_path('routes/spa.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
