<?php

// Simple test to check if server is working
echo "Server Test:\n";
echo "PHP Version: " . phpversion() . "\n";
echo "Current Directory: " . getcwd() . "\n";
echo "Laravel exists: " . (file_exists('artisan') ? 'YES' : 'NO') . "\n";

// Test if we can access Laravel
if (file_exists('artisan')) {
    echo "Laravel artisan file exists\n";
    
    // Try to run a simple artisan command
    $output = shell_exec('php artisan --version 2>&1');
    echo "Artisan version: " . $output . "\n";
    
    // Test routes
    $output = shell_exec('php artisan route:list --path=api/login 2>&1');
    echo "Login route: " . $output . "\n";
}

// Test database connection
try {
    require_once 'vendor/autoload.php';
    $app = require_once 'bootstrap/app.php';
    $app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
    
    $pdo = \Illuminate\Support\Facades\DB::connection()->getPdo();
    echo "Database connection: SUCCESS\n";
} catch (Exception $e) {
    echo "Database connection: FAILED - " . $e->getMessage() . "\n";
}
