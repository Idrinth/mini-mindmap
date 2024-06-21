<?php

namespace De\Idrinth\MiniMindmap\Controller;

use De\Idrinth\MiniMindmap\Repository\Node;
use De\Idrinth\MiniMindmap\Result;

class Latest
{
    public function __construct(private Node $node, private \De\Idrinth\MiniMindmap\Repository\Mindmap $mindmap)
    {}
    public function get(string $mindmap, string $since): Result
    {
        $result = new Result\Json();
        $result->setContent($this->node->changed($this->mindmap->uuidToId($mindmap), date('Y-m-d H:i:s', (int)$since -1)));
        return $result;
    }
}
