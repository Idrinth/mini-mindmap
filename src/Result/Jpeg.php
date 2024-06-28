<?php

namespace De\Idrinth\MiniMindmap\Result;

class Jpeg extends Base
{
    public function __construct()
    {
        parent::__construct('image/jpeg');
    }
    function send(): void
    {
        $this->sendHeaders();
        if (is_string($this->data)) {
            readfile(__DIR__ . '/../../resources/' . $this->data . '.jpg');
            return;
        }
        readfile(__DIR__ . '/../../uploads/' . $this->data['mindmap'] . '/' . $this->data['node'] . '.jpg');
    }
}
