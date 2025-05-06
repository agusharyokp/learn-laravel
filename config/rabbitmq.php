<?php

return [
    'host' => env('RABBITMQ_HOST', '127.0.0.1'),
    'port' => env('RABBITMQ_PORT', 5672),
    'user' => env('RABBITMQ_USER', 'rabbit'),
    'password' => env('RABBITMQ_PASSWORD', 'rabbit'),
    'vhost' => env('RABBITMQ_VHOST', '/'),
];
