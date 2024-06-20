<?php

namespace De\Idrinth\MiniMindmap\Result;

class Json extends Base
{
    public function __construct()
    {
        parent::__construct('application/json; charset=utf-8');
    }
    function send(): void
    {
        $this->sendHeaders();
        echo json_encode($this->data);
    }
}
