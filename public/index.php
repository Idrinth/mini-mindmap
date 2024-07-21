<?php

use De\Idrinth\MiniMindmap\Controller\Homepage;
use De\Idrinth\MiniMindmap\Controller\Image;
use De\Idrinth\MiniMindmap\Controller\Latest;
use De\Idrinth\MiniMindmap\Controller\Mindmap;
use De\Idrinth\MiniMindmap\Application;
use De\Idrinth\MiniMindmap\Controller\Scripts;
use De\Idrinth\MiniMindmap\Controller\Setup;
use De\Idrinth\MiniMindmap\Controller\Styles;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('UTC');

if (is_file(dirname(__DIR__) . '/.env')) {
    Dotenv::createImmutable(dirname(__DIR__))->load();
    exec(__DIR__ . '/../vendor/bin/phinx');
}

(new Application())
    ->parameter(PDO::class, 'dsn', $_ENV['DATABASE_DSN'] ?? null)
    ->parameter(PDO::class, 'username', $_ENV['DATABASE_USERNAME'] ?? null)
    ->parameter(PDO::class, 'password', $_ENV['DATABASE_PASSWORD'] ?? null)
    ->parameter(PDO::class, 'options', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION])
    ->register('get', '/', Homepage::class)
    ->register('get', '/styles.css', Styles::class)
    ->register('get', '/scripts.js', Scripts::class)
    ->register('get', '/mindmap', Mindmap::class, 'create')
    ->register('post', '/mindmap', Mindmap::class)
    ->register('get', '/setup', Setup::class)
    ->register('post', '/setup', Setup::class)
    ->register('get', '/mindmap/{mindmap}', Mindmap::class)
    ->register('delete', '/mindmap/{mindmap}', Mindmap::class)
    ->register('get', '/mindmap/{mindmap}/parent/{parent}', Mindmap::class, 'children')
    ->register('get', '/mindmap/{mindmap}/node/{node}', Mindmap::class, 'single')
    ->register('get', '/mindmap/{mindmap}/node/{node}/{format}', Mindmap::class, 'export')
    ->register('put', '/mindmap/{mindmap}/parent/{parent}', Mindmap::class)
    ->register('delete', '/mindmap/{mindmap}/node/{node}', Mindmap::class)
    ->register('patch', '/mindmap/{mindmap}/node/{node}', Mindmap::class)
    ->register('get', '/mindmap/{mindmap}/since/{since}', Latest::class)
    ->register('get', '/image/{mindmap}/{image}', Image::class)
    ->register('put', '/image/{mindmap}/{image}', Image::class)
    ->register('get', '/imm.svg', Image::class, 'logo')
    ->register('get', '/idrinth-mini-mindmap.jpg', Image::class, 'banner')
    ->register('get', '/arrow.svg', Image::class, 'arrow')
    ->handle($_SERVER['REQUEST_URI'] ?? '/', $_SERVER['REQUEST_METHOD'])
    ->send();
