<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        'App\Http\Middleware\HeadersMiddleware',      
        //'App\Http\Middleware\HttpsRedirect',
        'App\Http\Middleware\XmlEscape',
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            //\App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:60,1',
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            //\App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'sso' => \App\Http\Middleware\SsoMiddleware::class,
        'webhooks' => \App\Http\Middleware\WebhooksMiddleware::class,
        'headers' => \App\Http\Middleware\HeadersMiddleware::class,
        'quickbooks_admin' => \App\Http\Middleware\QuickbooksAdminMiddleware::class,
        'quickbooks_connection' => \App\Http\Middleware\QuickbooksConnection::class,
        'quickbooks_connection_false' => \App\Http\Middleware\QuickbooksConnectionFalseMiddleware::class,
        'debug_routes' => \App\Http\Middleware\DebugRoutes::class,
        'setup' => \App\Http\Middleware\SetupMiddleware::class,
        'setup_false' => \App\Http\Middleware\SetupFalseMiddleware::class,
        'auth_routes' => \App\Http\Middleware\AuthRoutesMiddleware::class,
        'invitation' => \App\Http\Middleware\InvitationMiddleware::class,
        'dev' => \App\Http\Middleware\DevMiddleware::class,
    ];
}
