<?php

namespace De\Idrinth\MiniMindmap\Controller;

use De\Idrinth\MiniMindmap\Result;
use De\Idrinth\MiniMindmap\Result\Png;

class Image
{
    public function get():  Result
    {
        return new Png();
    }
}