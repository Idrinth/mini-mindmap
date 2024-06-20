<?php

namespace De\Idrinth\MiniMindmap\Result;

class Png extends Base
{
    function send(): void
    {
        $this->sendHeaders();
        header('Content-Type: image/png');
        readfile(__DIR__ . '/../../uploads/' . $this->data['mindmap'] . '/' . $this->data['node'] . '.png');
    }
}