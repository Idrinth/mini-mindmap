<?php

namespace De\Idrinth\MiniMindmap\Exists;

class FileExists
{
    public function jpeg(string $mindmap, string $node)
    {
        return is_file(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.jpg');
    }
    public function png(string $mindmap, string $node)
    {
        return is_file(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.png');
    }
    public function gif(string $mindmap, string $node)
    {
        return is_file(__DIR__ . '/../uploads/' . $mindmap . '/' . $node . '.gif');
    }
}