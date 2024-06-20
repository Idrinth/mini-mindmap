<?php

namespace De\Idrinth\MiniMindmap\Entity;

class Mindmap
{
    public ?int $id;
    public string $uuid;
    public string $title;
    public ?int $customerId;
    public int $rootElementId;
}