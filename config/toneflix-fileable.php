<?php

return [
    'collections' => [
        'avatar' => [
            'size' => [400, 400],
            'path' => 'avatars/',
            'default' => 'default.png',
        ],
        'banner' => [
            'size' => [1200, 600],
            'path' => 'media/banners/',
            'default' => 'default.png',
        ],
        'default' => [
            'path' => 'media/container/',
            'default' => 'default.png',
        ],
        'logo' => [
            'size' => [200, 200],
            'path' => 'media/logos/',
            'default' => 'default.png',
        ],
        'private' => [
            'files' => [
                'path' => 'files/',
                'secure' => false,
            ],
            'images' => [
                'path' => 'files/images/',
                'default' => 'default.png',
                'secure' => true,
            ],
        ],
    ],
    'image_sizes' => [
        'xs' => '431',
        'sm' => '431',
        'md' => '694',
        'lg' => '720',
        'xl' => '1080',
    ],
    'file_route_secure_middleware' => 'window_auth',
    'file_route_secure' => 'files/secure/{file}',
    'file_route_open' => 'files/public/{file}',
    'image_templates' => [
    ],
    'symlinks' => [
        public_path('avatars') => storage_path('app/public/avatars'),
        public_path('media') => storage_path('app/public/media'),
    ],
];
