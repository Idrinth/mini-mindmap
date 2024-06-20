<?php

namespace De\Idrinth\MiniMindmap\Result;

class Jpeg extends Base
{
    function send(): void
    {
        $this->sendHeaders();
        header('Content-Type: image/jpeg');
        readfile(__DIR__ . '/../../uploads/' . $this->data['mindmap'] . '/' . $this->data['node'] . '.jpg');
    }
}