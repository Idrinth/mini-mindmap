<?php

namespace De\Idrinth\MiniMindmap\Controller;

use De\Idrinth\MiniMindmap\Result\Html;

class NotFound
{
    public function all() {
        $result = new Html();
        $result->setContent(['template' => 'not-found.twig']);
        return $result;
    }
}
