<?php

return [
    'paths' => [
        'migrations' => 'database/migrations',
        'seeds' => 'database/seeds'
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'production',
        'production' => [
            'adapter' => 'mysql',
            'host' => 'localhost',
            'name' => 'imm',
            'user' => 'root',
            'pass' => '',
            'port' => '3306',
            'charset' => 'utf8mb4',
        ],
    ],
    'version_order' => 'creation'
];
