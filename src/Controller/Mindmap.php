<?php

namespace De\Idrinth\MiniMindmap\Controller;

use De\Idrinth\MiniMindmap\Repository\Node;
use De\Idrinth\MiniMindmap\Result;
use De\Idrinth\MiniMindmap\Result\Html;
use De\Idrinth\MiniMindmap\Result\Json;
use Webmozart\Assert\Assert;

class Mindmap
{
    private \De\Idrinth\MiniMindmap\Repository\Mindmap $mindmap;
    private Node $node;
    public function get(string $id): Result
    {
        Assert::uuid($id);
        $mindmapId = $this->mindmap->uuidToId($id);
        $result = new Html();
        $mindmap =  $this->mindmap->get($mindmapId);
        $result->setContent([
            'template' => 'mindmap.twig',
            'mindmap' => $mindmap,
            'root' => $this->node->get($mindmap->rootElementId),
            'nodes'  => $this->node->getChildren($mindmap->rootElementId),
        ]);
        return $result;
    }
    public function single(string $id, string $parent): Result
    {
        Assert::uuid($id);
        Assert::uuid($parent);
        $mindmapId = $this->mindmap->uuidToId($id);
        $parentId = $this->node->uuidToId($mindmapId, $parent);
        $result = new Json();
        $result->setContent($this->node->get($parentId));
        return $result;
    }
    public function children(string $id, string $parent): Result
    {
        Assert::uuid($id);
        Assert::uuid($parent);
        $mindmapId = $this->mindmap->uuidToId($id);
        $parentId = $this->node->uuidToId($mindmapId, $parent);
        $result = new Json();
        $result->setContent($this->node->getChildren($parentId));
        return $result;
    }
}