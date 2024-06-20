<?php

namespace De\Idrinth\MiniMindmap\Controller;

use De\Idrinth\MiniMindmap\Result;
use De\Idrinth\MiniMindmap\Result\Html;

class Setup
{
    public function get(): Result
    {
        $result = new Html();
        $result->setContent([
            'template' => 'setup.twig',
        ]);
        return $result;
    }
    public function post(): Result
    {

    }
}
