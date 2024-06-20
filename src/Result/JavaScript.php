<?php

namespace De\Idrinth\MiniMindmap\Result;

class JavaScript extends Base
{
    public function __construct()
    {
        parent::__construct('application/javascript');
    }

    function send(): void
    {
        $this->sendHeaders();
        readfile(__DIR__ . '/../../resources/' . $this->data . '.js');
    }
}
