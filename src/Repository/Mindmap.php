<?php

namespace De\Idrinth\MiniMindmap\Repository;

use Ramsey\Uuid\Uuid;

class Mindmap
{
    public function uuidToId(string $uuid): int
    {
        return 11;
    }
    public function get(string $id): \De\Idrinth\MiniMindmap\Entity\Mindmap
    {
        return new \De\Idrinth\MiniMindmap\Entity\Mindmap();
    }
    public function create(string $title): \De\Idrinth\MiniMindmap\Entity\Mindmap
    {
        $mindmap = new \De\Idrinth\MiniMindmap\Entity\Mindmap();
        $mindmap->uuid = Uuid::uuid4()->toString();
        $mindmap->title = $title;
        $mindmap->id = 213;
        $mindmap->rootElementId = 122314;
        return $mindmap;
    }
}