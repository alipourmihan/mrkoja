<?php

// Test routes directly
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Routes:\n";

// Get all routes
$routes = \Illuminate\Support\Facades\Route::getRoutes();

foreach ($routes as $route) {
    if (strpos($route->uri(), 'api/') === 0) {
        echo "Route: " . $route->methods()[0] . " " . $route->uri() . " -> " . $route->getActionName() . "\n";
    }
}

echo "\nTesting specific routes:\n";

// Test login route
$loginRoute = $routes->getByName('api.login') ?? $routes->first(function ($route) {
    return $route->uri() === 'api/login' && in_array('POST', $route->methods());
});

if ($loginRoute) {
    echo "Login route found: " . $loginRoute->methods()[0] . " " . $loginRoute->uri() . "\n";
} else {
    echo "Login route NOT found!\n";
}

// Test logout route
$logoutRoute = $routes->first(function ($route) {
    return $route->uri() === 'api/logout' && in_array('POST', $route->methods());
});

if ($logoutRoute) {
    echo "Logout route found: " . $logoutRoute->methods()[0] . " " . $logoutRoute->uri() . "\n";
} else {
    echo "Logout route NOT found!\n";
}
