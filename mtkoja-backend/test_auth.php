<?php

// Test API endpoints
$baseUrl = 'https://api.mrkoja.com/api';

// Test login endpoint
echo "Testing login endpoint...\n";
$loginData = [
    'email' => 'admin@mrkoja.com',
    'password' => 'admin123'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login Response Code: " . $httpCode . "\n";
echo "Login Response: " . $response . "\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (isset($data['token'])) {
        $token = $data['token'];
        echo "Token received: " . substr($token, 0, 20) . "...\n\n";
        
        // Test logout endpoint
        echo "Testing logout endpoint...\n";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/logout');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $logoutResponse = curl_exec($ch);
        $logoutCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "Logout Response Code: " . $logoutCode . "\n";
        echo "Logout Response: " . $logoutResponse . "\n";
    }
}
