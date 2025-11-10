<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\User;
use Illuminate\Support\Facades\Hash;

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Create admin user
$email = 'admin@mrkoja.com';
$password = 'admin123';
$name = 'Admin User';

// Check if user already exists
if (User::where('email', $email)->exists()) {
    echo "User with this email already exists!\n";
    exit(1);
}

// Create admin user
$user = User::create([
    'name' => $name,
    'email' => $email,
    'password' => Hash::make($password),
    'role' => 'admin',
    'status' => 'active',
]);

// Create personal access token
$token = $user->createToken('admin-token')->plainTextToken;

echo "Admin user created successfully!\n";
echo "Email: " . $email . "\n";
echo "Name: " . $name . "\n";
echo "Token: " . $token . "\n";
echo "Role: admin\n";
