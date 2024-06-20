<?php

namespace De\Idrinth\MiniMindmap\Result;

class Png extends Base
{
    public function __construct()
    {
        parent::__construct('image/png');
    }
    function send(): void
    {
        $this->sendHeaders();
        header('Content-Type: image/png');
        readfile(__DIR__ . '/../../uploads/' . $this->data['mindmap'] . '/' . $this->data['node'] . '.png');
    }
}
