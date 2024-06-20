<?php

namespace De\Idrinth\MiniMindmap\Result;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Html extends Base
{
    public function __construct()
    {
        parent::__construct('text/html; charset=utf-8');
    }
    function send(): void
    {
        $this->sendHeaders();
        (new Environment(new FilesystemLoader(__DIR__ . '/../../templates')))->display($this->data['template'], $this->data);
    }
}
