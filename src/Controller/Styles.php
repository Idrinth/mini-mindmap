<?php

namespace De\Idrinth\MiniMindmap\Controller;

use De\Idrinth\MiniMindmap\Result;

class Styles
{
    public function get(): Result
    {
        $result = new Result\StyleSheet();
        $result->setContent('styles');
        return $result;
    }
}
