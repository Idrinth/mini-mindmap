<?php

namespace De\Idrinth\MiniMindmap\Entity;

class Node
{
    public ?int $id;
    public string $uuid;
    public ?int $parentId;
    public string $text;
    public ?string $description;
    public ?string $image = null;
    public int $mindmapId;
    public ?string $parentUuid = null;
}
