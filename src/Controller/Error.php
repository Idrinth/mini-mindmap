<?php

namespace De\Idrinth\MiniMindmap\Controller;

use De\Idrinth\MiniMindmap\Result\Html;

class Error
{
    public function all() {
        $result = new Html();
        $result->setStatusCode(500);
        $result->setContent([
            'template' => 'error.twig'
        ]);
        return $result;
    }
}
