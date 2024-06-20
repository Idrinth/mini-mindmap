<?php

namespace De\Idrinth\MiniMindmap\Result;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class Html extends Base
{
    function send(): void
    {
        $this->sendHeaders();
        header('Content-Type: text/html; charset=utf-8');
        (new Environment(new FilesystemLoader(__DIR__ . '/../../templates')))->display($this->data['template'], $this->data);
    }
}