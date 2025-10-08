<?php

// تست ساده برای API تصاویر
$baseUrl = 'http://localhost:8000/api';

// ابتدا لاگین کنید
$loginData = [
    'email' => 'owner@mtkoja.com',
    'password' => 'password'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl . '/login');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($loginData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Login Response (HTTP $httpCode):\n";
echo $response . "\n\n";

if ($httpCode === 200) {
    $data = json_decode($response, true);
    if (isset($data['token'])) {
        $token = $data['token'];
        echo "Token received: " . substr($token, 0, 20) . "...\n\n";
        
        // تست دریافت کسب و کارها
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $baseUrl . '/my-businesses');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $token,
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "My Businesses Response (HTTP $httpCode):\n";
        echo $response . "\n\n";
        
        // اگر کسب و کاری وجود دارد، تست دریافت تصاویر
        $businesses = json_decode($response, true);
        if (isset($businesses['businesses']['data']) && count($businesses['businesses']['data']) > 0) {
            $businessId = $businesses['businesses']['data'][0]['id'];
            echo "Testing image API for business ID: $businessId\n";
            
            // تست دریافت تصاویر
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $baseUrl . "/businesses/$businessId/images");
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $token,
                'Accept: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            echo "Business Images Response (HTTP $httpCode):\n";
            echo $response . "\n\n";
        }
    }
} else {
    echo "Login failed!\n";
}
