<?php

namespace De\Idrinth\MiniMindmap\Controller;

use De\Idrinth\MiniMindmap\NotFoundException;
use De\Idrinth\MiniMindmap\Repository\Node;
use De\Idrinth\MiniMindmap\Result;
use De\Idrinth\MiniMindmap\Result\Gif;
use De\Idrinth\MiniMindmap\Result\Jpeg;
use De\Idrinth\MiniMindmap\Result\NoContent;
use De\Idrinth\MiniMindmap\Result\Png;
use De\Idrinth\MiniMindmap\Result\Svg;
use Webmozart\Assert\Assert;

class Image
{
    public function __construct(private \De\Idrinth\MiniMindmap\Repository\Mindmap $mindmap, private Node $node)
    {
    }
    public function get(string $mindmap, string $node):  Result
    {
        $nodeElement = $this->node->get($this->node->uuidToId($this->mindmap->uuidToId($mindmap), $node));
        if ($nodeElement->image === null) {
            throw new NotFoundException();
        }
        if (is_file(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.' . $nodeElement->image)) {
            $file = match ($nodeElement->image) {
                'png' => new Png(),
                'jpeg' => new Jpeg(),
                'gif' => new Gif(),
                default => throw new NotFoundException(),
            };
            $file->setContent(['mindmap' => $mindmap, 'node' => $node]);
            return $file;
        }
        throw new NotFoundException();
    }
    public function put(string $mindmap, string $node):  Result
    {
        Assert::uuid($mindmap);
        Assert::uuid($node);
        $mindmapId = $this->mindmap->uuidToId($mindmap);
        if ($mindmapId === 0) {
            throw new NotFoundException();
        }
        $nodeId = $this->node->uuidToId($mindmapId, $node);
        if ($nodeId === 0) {
            throw new NotFoundException();
        }
        if (is_file(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.jpeg')) {
            unlink(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.jpeg');
        }
        if (is_file(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.png')) {
            unlink(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.png');
        }
        if (is_file(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.gif')) {
            unlink(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.gif');
        }
        $image = file_get_contents('php://input');
        file_put_contents(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.jpeg', $image);
        file_put_contents(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.png', $image);
        file_put_contents(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.gif', $image);
        $ext = ['jpg', 'png', 'gif'];
        if (! imagecreatefrompng(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.png')) {
            unlink(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.png');
            $ext = array_filter($ext, function ($ext) {
                return $ext !== 'png';
            });
        }
        if (! imagecreatefromjpeg(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.jpeg')) {
            unlink(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.jpeg');
            $ext = array_filter($ext, function ($ext) {
                return $ext !== 'jpg';
            });
        }
        if (! imagecreatefromgif(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.gif')) {
            unlink(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.gif');
            $ext = array_filter($ext, function ($ext) {
                return $ext !== 'gif';
            });
        }
        $this->node->image($nodeId, $ext[0]??null);
        return new NoContent();
    }
    public function logo():  Result
    {
        $result = new Svg();
        $result->setContent('imm');
        return $result;
    }
    public function banner():  Result
    {
        $result = new Jpeg();
        $result->setContent('idrinth-mini-mindmap');
        return $result;
    }
    public function arrow():  Result
    {
        $result = new Svg();
        $result->setContent('arrow');
        return $result;
    }
}
