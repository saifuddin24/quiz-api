<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use function foo\func;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {

        $this->mapApiRoutes();

        $this->apiDocumentsRoutes( );
        //$this->mapWebRoutes( );

    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function apiDocumentsRoutes()
    {
        Route::prefix('/api-documentation/v1')
            ->middleware( ['web'] )
            ->namespace(  'App\Http\Controllers\DocControllers' )
            ->group(function(){
                Route::get('/', 'Documentation@index' );
            });
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {

        Route::prefix('api/v1')->group(function (){

            Route::prefix( "admin" )->group(function(){

                Route::middleware( ['auth:api', 'admin'] )->group(function (){

                    Route::namespace($this->namespace)->group( base_path('routes/admin-only.php' ) );

                    Route::namespace($this->namespace)->group( base_path('routes/api.php') );
                });
            });

            Route::middleware('api')->namespace($this->namespace)->group(base_path('routes/api.php'));
        });

    }

}
