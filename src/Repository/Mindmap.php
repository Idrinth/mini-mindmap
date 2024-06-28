<?php

namespace De\Idrinth\MiniMindmap\Repository;

use De\Idrinth\MiniMindmap\NotFoundException;
use PDO;
use Ramsey\Uuid\Uuid;

class Mindmap
{
    public function  __construct(private PDO $database,  private Node $node)
    {}
    public function count()
    {
        $mindmap = $this->database->prepare('SELECT Count(id) FROM mindmap');
        $mindmap->execute([]);
        return (int) $mindmap->fetchColumn();
    }
    public function uuidToId(string $uuid): int
    {
        $mindmap = $this->database->prepare('SELECT id FROM mindmap WHERE uuid=:uuid');
        $mindmap->execute([
            ':uuid' => $uuid,
        ]);
        return (int) $mindmap->fetchColumn();
    }
    public function get(int $id): \De\Idrinth\MiniMindmap\Entity\Mindmap
    {
        $mindmap = $this->database->prepare('SELECT id,uuid,root_element_id AS rootElementId,title FROM mindmap WHERE id=:id');
        $mindmap->execute([
            ':id' => $id,
        ]);
        $mindmap->setFetchMode(PDO::FETCH_CLASS,  \De\Idrinth\MiniMindmap\Entity\Mindmap::class);
        return $mindmap->fetch() ?: throw new NotFoundException();
    }
    public function create(string $title): \De\Idrinth\MiniMindmap\Entity\Mindmap
    {
        $mindmap = $this->database->prepare('INSERT INTO mindmap(uuid, title, root_element_id, customer_id) VALUES (:uuid, :title, null, null)');
        $mindmap->execute([
            ':uuid' => Uuid::uuid4()->toString(),
            ':title' => $title,
        ]);
        $id = $this->database->lastInsertId();
        $initialNode = $this->node->create($title, '',  $id);
        $this->database->exec("UPDATE mindmap SET root_element_id={$initialNode->id} WHERE id=$id");
        return $this->get($id);
    }

    public function update(int $id, mixed $title): void
    {
        $mindmap = $this->database->prepare('UPDATE mindmap SET updated_at=:now, title=:title WHERE id=:id');
        $mindmap->execute([
            ':id' => $id,
            ':now' => date('Y-m-d H:i:s'),
            ':title' => $title,
        ]);
    }
}
