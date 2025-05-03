<?php

use App\Http\Middleware\CanEditProducts;
use App\Http\Middleware\CanReadMessages;
use App\Http\Middleware\HasAdminPanelAccess;
use App\Http\Middleware\IsAdmin;
use App\Http\Middleware\IsBanned;
use App\Http\Middleware\IsVendor;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\ThemeMiddleware;
use App\Http\Middleware\VerifyLogin;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(ThemeMiddleware::class);
        $middleware->alias([
            'verify_2fa' => VerifyLogin::class,
            'is_vendor' => IsVendor::class,
            'is_admin' => IsAdmin::class,
            'is_banned' => IsBanned::class,
            'admin_panel_access' => HasAdminPanelAccess::class,
            'can_edit_products' => CanEditProducts::class,
            'can_read_messages' => CanReadMessages::class,
            'guest'=> RedirectIfAuthenticated::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
