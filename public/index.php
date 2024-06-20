<?php

use De\Idrinth\MiniMindmap\Controller\Homepage;
use De\Idrinth\MiniMindmap\Controller\Image;
use De\Idrinth\MiniMindmap\Controller\Latest;
use De\Idrinth\MiniMindmap\Controller\Mindmap;
use De\Idrinth\MiniMindmap\Application;
use De\Idrinth\MiniMindmap\Controller\Setup;

require_once __DIR__ . '/../vendor/autoload.php';

(new Application())
    ->parameter(PDO::class, 'dsn', $_ENV['DATABASE_DSN'] ?? null)
    ->parameter(PDO::class, 'username', $_ENV['DATABASE_USERNAME'] ?? null)
    ->parameter(PDO::class, 'password', $_ENV['DATABASE_PASSWORD'] ?? null)
    ->register('get', '/', Homepage::class)
    ->register('get', '/mindmap', Mindmap::class, 'create')
    ->register('post', '/mindmap', Mindmap::class)
    ->register('get', '/setup', Setup::class)
    ->register('post', '/setup', Setup::class)
    ->register('get', '/mindmap/{mindmap}', Mindmap::class)
    ->register('delete', '/mindmap/{mindmap}', Mindmap::class)
    ->register('get', '/mindmap/{mindmap}/parent/{parent}', Mindmap::class, 'children')
    ->register('get', '/mindmap/{mindmap}/node/{node}', Mindmap::class, 'single')
    ->register('put', '/mindmap/{mindmap}/parent/{parent}', Mindmap::class)
    ->register('delete', '/mindmap/{mindmap}/node/{node}', Mindmap::class)
    ->register('post', '/mindmap/{mindmap}/node/{node}', Mindmap::class)
    ->register('get', '/modified/{mindmap}/update/{latest}', Latest::class)
    ->register('get', '/image/{mindmap}/{image}.(jpg|png|gif)', Image::class)
    ->handle($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'])
    ->send();
