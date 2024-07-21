<?php

use Dotenv\Dotenv;

require_once __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->load();

return [
    'paths' => [
        'migrations' => 'database/migrations',
        'seeds' => 'database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'production',
        'production' => [
            'connection' => new PDO($_ENV['DATABASE_DSN'], $_ENV['DATABASE_USERNAME'],$_ENV['DATABASE_PASSWORD']),
            'name' => $_ENV['DATABASE_NAME'] ?? 'imm',
            'charset' => 'utf8mb4',
        ],
    ],
    'version_order' => 'creation'
];
