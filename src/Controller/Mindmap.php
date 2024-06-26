<?php

namespace De\Idrinth\MiniMindmap\Controller;

use De\Idrinth\MiniMindmap\Repository\Node;
use De\Idrinth\MiniMindmap\Result;
use De\Idrinth\MiniMindmap\Result\Html;
use De\Idrinth\MiniMindmap\Result\Json;
use Ramsey\Uuid\Uuid;
use Throwable;
use Webmozart\Assert\Assert;

class Mindmap
{
    public  function __construct(private Node $node, private \De\Idrinth\MiniMindmap\Repository\Mindmap $mindmap)
    {
    }
    public function get(string $id): Result
    {
        Assert::uuid($id);
        $mindmapId = $this->mindmap->uuidToId($id);
        $result = new Html();
        $mindmap = $this->mindmap->get($mindmapId);
        $result->setContent([
            'template' => 'mindmap.twig',
            'title_prefix' => $mindmap->title . ' | ',
            'mindmap' => $mindmap,
            'root' => $this->node->get($mindmap->rootElementId),
        ]);
        return $result;
    }
    public function put(string $id, string $parent): Result
    {
        Assert::uuid($id);
        Assert::uuid($parent);
        $result = new Json();
        $mindmap = $this->mindmap->uuidToId($id);
        $result->setContent($this->node->create($_POST['text'], $_POST['description'], $mindmap, $this->node->uuidToId($mindmap, $parent)));
        return $result;
    }
    public function create(): Result
    {
        $result = new Html();
        $result->setContent([
            'template' => 'mindmap-create.twig',
            'title_prefix' => 'Create Mindmap | ',
        ]);
        return $result;
    }
    public function post(): Result
    {
        $result = new Html();
        $result->setContent([
            'template' => 'mindmap-create.twig',
        ]);
        Assert::notEmpty($_POST['name'] ?? '');
        $mindmap = $this->mindmap->create($_POST['name']);
        $result->setStatusCode(303);
        $result->addHeader('Location', '/mindmap/' . $mindmap->uuid);
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
    public function patch(string $id, string $node): Result
    {
        Assert::uuid($id);
        Assert::uuid($node);
        $mindmapId = $this->mindmap->uuidToId($id);
        $nodeId = $this->node->uuidToId($mindmapId, $node);
        $result = new Json();
        $result->setContent($this->node->patch($nodeId, $_POST));
        return $result;
    }
    public function delete(string $id, string $node): Result
    {
        Assert::uuid($id);
        Assert::uuid($node);
        $mindmapId = $this->mindmap->uuidToId($id);
        $nodeId = $this->node->uuidToId($mindmapId, $node);
        $result = new Result\NoContent();
        $this->node->delete($nodeId);
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
