<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Create admin user if not exists
$admin = User::firstOrCreate(
    ['email' => 'admin@test.com'],
    [
        'name' => 'Admin User',
        'password' => Hash::make('password'),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]
);

echo "Admin user created/found with ID: " . $admin->id . "\n";
echo "Email: " . $admin->email . "\n";
echo "Role: " . $admin->role . "\n";


