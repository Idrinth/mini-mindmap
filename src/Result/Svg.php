<?php

namespace De\Idrinth\MiniMindmap\Result;

class Svg extends Base
{
    public function __construct()
    {
        parent::__construct('image/svg+xml');
    }
    function send(): void
    {
        $this->sendHeaders();
        readfile(__DIR__ . '/../../resources/' . $this->data . '.svg');
    }
}
