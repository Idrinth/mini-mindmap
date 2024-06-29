<?php

namespace De\Idrinth\MiniMindmap\Controller;

use De\Idrinth\MiniMindmap\Result;
use De\Idrinth\MiniMindmap\Result\Jpeg;
use De\Idrinth\MiniMindmap\Result\Png;
use De\Idrinth\MiniMindmap\Result\Svg;

class Image
{
    public function get():  Result
    {
        return new Png();
    }
    public function put():  Result
    {
        return new Png();
    }
    public function logo():  Result
    {
        $result = new Svg();
        $result->setContent('imm');
        return $result;
    }
    public function banner():  Result
    {
        $result = new Jpeg();
        $result->setContent('idrinth-mini-mindmap');
        return $result;
    }
}
