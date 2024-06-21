<?php

namespace De\Idrinth\MiniMindmap\Result;

class NoContent extends Base
{
    public function __construct()
    {
        parent::__construct('text/plain');
    }

    public function send(): void
    {
        $this->sendHeaders();
    }
}
