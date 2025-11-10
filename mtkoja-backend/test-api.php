<?php
// Test API directly without Laravel
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Test provinces data
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
?>


