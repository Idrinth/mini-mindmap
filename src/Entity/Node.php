<?php

namespace De\Idrinth\MiniMindmap\Entity;

use JsonSerializable;

class Node implements JsonSerializable
{
    public ?int $id;
    public string $uuid;
    public ?int $parentId;
    public string $text;
    public ?string $description;
    public ?string $image = null;
    public int $mindmapId;
    public ?string $parentUuid = null;
    public int $deleted;

    public function jsonSerialize(): mixed
    {
        return [
            "uuid" => $this->uuid,
            "parentUuid" => $this->parentUuid,
            "text" => $this->text,
            "description" => $this->description,
            "image" => $this->image,
        ];
    }
}
