<?php

namespace De\Idrinth\MiniMindmap\Controller;

use De\Idrinth\MiniMindmap\Result;
use De\Idrinth\MiniMindmap\Result\Html;

class Setup
{
    public function get(): Result
    {
        if (is_file(dirname(__DIR__, 2) . '/.env')) {
            $result = new Html();
            $result->setStatusCode(303);
            $result->addHeader('Location', '/');
            $result->setContent(['template' => 'empty.twig']);
            return $result;
        }
        $result = new Html();
        $result->setContent([
            'template' => 'setup.twig',
        ]);
        return $result;
    }
    public function post(): Result
    {
        if (is_file(dirname(__DIR__, 2) . '/.env')) {
            $result = new Html();
            $result->setStatusCode(303);
            $result->addHeader('Location', '/');
            $result->setContent(['template' => 'empty.twig']);
            return $result;
        }
        $content = "DATABASE_DSN={$_POST['dsn']}\nDATABASE_NAME={$_POST['name']}\nDATABASE_USERNAME={$_POST['username']}\nDATABASE_PASSWORD={$_POST['password']}\n";
        file_put_contents(dirname(__DIR__, 2) . '/.env', $content);
        exec('php "' .dirname(__DIR__, 2) . '/vendor/bin/phinx" migrate');
        $result = new Html();
        $result->setStatusCode(303);
        $result->addHeader('Location', '/');
        $result->setContent([
            'template' => 'empty.twig',
        ]);
        return $result;
    }
}
