<?php

namespace De\Idrinth\MiniMindmap\Result;

class StyleSheet extends Base
{
    public function __construct()
    {
        parent::__construct('text/css');
    }

    function send(): void
    {
        $this->sendHeaders();
        readfile(__DIR__ . '/../../resources/' . $this->data . '.css');
    }
}
