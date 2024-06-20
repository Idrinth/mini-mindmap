<?php

namespace De\Idrinth\MiniMindmap\Repository;

use Ramsey\Uuid\Uuid;

class Node
{
    public function uuidToId(int $mindmap, string $uuid): int
    {
        return 11;
    }
    public function get(int $id): \De\Idrinth\MiniMindmap\Entity\Node
    {
        $node = new \De\Idrinth\MiniMindmap\Entity\Node();
        $node->id = $id;
        $node->uuid = Uuid::uuid4();
        $node->text = "node example";
        return $node;
    }

    /**
     * @return \De\Idrinth\MiniMindmap\Entity\Node[]
     */
    public function getChildren(int $id): array
    {
        return [
            $this->get(12243),
            $this->get(12273),
            $this->get(16243),
        ];
    }
    public function create(string $text, string $description): \De\Idrinth\MiniMindmap\Entity\Node
    {
        $node = new \De\Idrinth\MiniMindmap\Entity\Node();
        $node->uuid = Uuid::uuid4()->toString();
        $node->parentId = 123;
        $node->mindmapId = 12;
        $node->description = $description;
        $node->text  = $text;
        $node->id = 213;
        return $node;
    }
}
