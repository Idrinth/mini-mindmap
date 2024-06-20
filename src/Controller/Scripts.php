<?php

namespace De\Idrinth\MiniMindmap\Controller;

use De\Idrinth\MiniMindmap\Result;

class Scripts
{
    public function get(): Result
    {
        $result = new Result\JavaScript();
        $result->setContent('scripts');
        return $result;
    }
}
