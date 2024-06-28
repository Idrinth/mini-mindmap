<?php

namespace De\Idrinth\MiniMindmap\Controller;

use De\Idrinth\MiniMindmap\Repository\Node;
use De\Idrinth\MiniMindmap\Result;
use De\Idrinth\MiniMindmap\Result\Html;

class Homepage
{
    public function __construct(private Node $nodes, private \De\Idrinth\MiniMindmap\Repository\Mindmap $mindmap)
    {

    }
    public function get(): Result
    {
        $result = new Html();
        $result->setContent([
            'template' => 'home.twig',
            'mindmaps' => $this->mindmap->count(),
            'nodes' => $this->nodes->count(),
        ]);
        return $result;
    }
}
