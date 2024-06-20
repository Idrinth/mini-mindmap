<?php

namespace De\Idrinth\MiniMindmap\Controller;

use De\Idrinth\MiniMindmap\Result;
use De\Idrinth\MiniMindmap\Result\Html;

class Homepage
{
    public function get(): Result
    {
        return new Html();
    }
}