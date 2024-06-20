<?php

namespace De\Idrinth\MiniMindmap\Result;

class Json extends Base
{
    function send(): void
    {
        $this->sendHeaders();
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($this->data);
    }
}