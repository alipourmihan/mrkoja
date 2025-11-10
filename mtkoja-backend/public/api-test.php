<?php
// Simple API test without Laravel
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Get the request URI
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);

// Route handling
switch ($path) {
    case '/api/provinces':
        $provinces = [
            [
                'id' => 1,
                'name' => 'تهران',
                'slug' => 'tehran',
                'is_active' => true,
                'sort_order' => 0,
                'created_at' => '2024-01-01T00:00:00.000000Z',
                'updated_at' => '2024-01-01T00:00:00.000000Z'
            ],
            [
                'id' => 2,
                'name' => 'اصفهان',
                'slug' => 'isfahan',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => '2024-01-01T00:00:00.000000Z',
                'updated_at' => '2024-01-01T00:00:00.000000Z'
            ],
            [
                'id' => 3,
                'name' => 'فارس',
                'slug' => 'fars',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => '2024-01-01T00:00:00.000000Z',
                'updated_at' => '2024-01-01T00:00:00.000000Z'
            ]
        ];
        
        echo json_encode([
            'provinces' => $provinces
        ]);
        break;
        
    case '/api/cities':
        $cities = [
            [
                'id' => 1,
                'name' => 'تهران',
                'slug' => 'tehran',
                'province_id' => 1,
                'is_active' => true,
                'sort_order' => 0,
                'created_at' => '2024-01-01T00:00:00.000000Z',
                'updated_at' => '2024-01-01T00:00:00.000000Z'
            ],
            [
                'id' => 2,
                'name' => 'کرج',
                'slug' => 'karaj',
                'province_id' => 1,
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => '2024-01-01T00:00:00.000000Z',
                'updated_at' => '2024-01-01T00:00:00.000000Z'
            ]
        ];
        
        echo json_encode([
            'cities' => $cities
        ]);
        break;
        
    case '/api/neighborhoods':
        $neighborhoods = [
            [
                'id' => 1,
                'name' => 'تجریش',
                'slug' => 'tajrish',
                'city_id' => 1,
                'is_active' => true,
                'sort_order' => 0,
                'created_at' => '2024-01-01T00:00:00.000000Z',
                'updated_at' => '2024-01-01T00:00:00.000000Z'
            ]
        ];
        
        echo json_encode([
            'neighborhoods' => $neighborhoods
        ]);
        break;
        
    default:
        http_response_code(404);
        echo json_encode([
            'error' => 'Route not found',
            'path' => $path
        ]);
        break;
}
?>


