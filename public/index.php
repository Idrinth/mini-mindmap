<?php

use De\Idrinth\MiniMindmap\Controller\Homepage;
use De\Idrinth\MiniMindmap\Controller\Image;
use De\Idrinth\MiniMindmap\Controller\Latest;
use De\Idrinth\MiniMindmap\Controller\Mindmap;
use De\Idrinth\MiniMindmap\Application;

require_once __DIR__ . '/../vendor/autoload.php';
new PDO();

(new Application())
    ->parameter(PDO::class, 'dsn', $_ENV['DATABASE_DSN'])
    ->parameter(PDO::class, 'username', $_ENV['DATABASE_USERNAME'])
    ->parameter(PDO::class, 'password', $_ENV['DATABASE_PASSWORD'])
    ->register('get', '/', Homepage::class)
    ->register('post', '/mindmap', Mindmap::class)
    ->register('get', '/mindmap/{uuid}', Mindmap::class)
    ->register('delete', '/mindmap/{uuid}', Mindmap::class)
    ->register('get', '/mindmap/{uuid}/parent/{uuid}', Mindmap::class, 'children')
    ->register('get', '/mindmap/{uuid}/node/{uuid}', Mindmap::class, 'single')
    ->register('put', '/mindmap/{uuid}/parent/{uuid}', Mindmap::class)
    ->register('delete', '/mindmap/{uuid}/node/{uuid}', Mindmap::class)
    ->register('post', '/mindmap/{uuid}/node/{uuid}', Mindmap::class)
    ->register('get', '/modified/{uuid}/update/{uuid}', Latest::class)
    ->register('get', '/image/{uuid}/{uuid}.(jpg|png|gif)', Image::class)
    ->handle($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'])
    ->send();
