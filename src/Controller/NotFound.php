<?php

namespace De\Idrinth\MiniMindmap\Controller;

use De\Idrinth\MiniMindmap\Result\Html;

class NotFound
{
    public function all() {
        return new Html();
    }
}