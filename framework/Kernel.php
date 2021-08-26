<?php

namespace Framework;

use Framework\authentication\AuthManager;
use Framework\middlewares\AuthMiddleware;

class Kernel
{
    /**
     * The application global HTTP middleware stack
     *
     * These middleware are run during every request to your application
     *
     * @var array
     */
    protected array $middleware = [
        AuthMiddleware::class,
//        TrustProxies::class,
//        CheckForMaintenanceMode::class,
//        ValidatePostSize::class,
//        TrimStrings::class,
//        ConvertEmptyStringsToNull::class,

//        AuthMiddleware::class, //...
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
//        EncryptCookies::class, // ....
//        StartSession::class, // ....
//        VerifyCsrfToken::class, // ....
//        SubstituteBindings::class, // ....
    ];

    protected $routerMiddleware = [
//        'auth' => AuthManager::class,
//        'auth.basic' => BasicAuth::class,
//        'can' => Authorize::class,
//        'guest' => RedirectIfAuthenticated::class,
//        'signed' => ValidateSignature::class,
//        'verified' => EnsureEmailIsVerified::class,
// must be used as e.g. ['middleware' => 'is_admin'] or ->middleware('is_admin')
//        'is_admin' => IsAdminMiddleware::class,

    ];
}
