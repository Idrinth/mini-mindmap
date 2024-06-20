<?php

namespace De\Idrinth\MiniMindmap\Result;


class Gif extends Base
{
    public function __construct()
    {
        parent::__construct('image/gif');
    }

    function send(): void
    {
        $this->sendHeaders();
        readfile(__DIR__ . '/../../uploads/' . $this->data['mindmap'] . '/' . $this->data['node'] . '.gif');
    }
}
