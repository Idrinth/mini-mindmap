<?php

namespace De\Idrinth\MiniMindmap\Result;


class Gif extends Base
{
    function send(): void
    {
        $this->sendHeaders();
        header('Content-Type: image/gif');
        readfile(__DIR__ . '/../../uploads/' . $this->data['mindmap'] . '/' . $this->data['node'] . '.gif');
    }
}