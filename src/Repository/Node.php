<?php

namespace De\Idrinth\MiniMindmap\Repository;

use De\Idrinth\MiniMindmap\NotFoundException;
use PDO;
use Ramsey\Uuid\Uuid;

class Node
{
    public function  __construct(private PDO $database)
    {}
    public function uuidToId(int $mindmap, string $uuid): int
    {
        $node = $this->database->prepare('SELECT id FROM node WHERE uuid=:uuid and mindmap_id=:mindmap');
        $node->execute([
            ':uuid' => $uuid,
            ':mindmap' => $mindmap,
        ]);
        return (int) $node->fetchColumn();
    }
    public function get(int $id): \De\Idrinth\MiniMindmap\Entity\Node
    {
        $node = $this->database->prepare('SELECT id,uuid,parent_id AS parentId,`text`,description FROM node WHERE id=:id');
        $node->execute([
            ':id' => $id,
        ]);
        $node->setFetchMode(PDO::FETCH_CLASS, \De\Idrinth\MiniMindmap\Entity\Node::class);
        return $node->fetch() ?: throw new NotFoundException();
    }

    /**
     * @return \De\Idrinth\MiniMindmap\Entity\Node[]
     */
    public function getChildren(int $id): array
    {
        $result = [];
        foreach ($this->database->query("SELECT id FROM node WHERE parent_id=$id", PDO::FETCH_OBJ)->fetchAll(PDO::FETCH_OBJ) as $node) {
            $result[] = $this->get($node->id);
        }
        return $result;
    }
    public function patch(int $id, array $patch): \De\Idrinth\MiniMindmap\Entity\Node
    {
        $node = $this->get($id);
        $statement = $this->database->prepare('UPDATE node SET `text`=:text, description=:description WHERE id=:id');
        $statement->execute([
            'id' => $id,
            'text' => $patch['text'] ??  $node->text,
            'description' => $patch['description'] ?? $node->description,
        ]);
        return $this->get($id);
    }
    public function delete(int $id): void
    {
        $this->database->exec("DELETE FROM node WHERE id=$id");
    }
    public function create(string $text, string $description, int $mindmapId, ?int $parentId = null): \De\Idrinth\MiniMindmap\Entity\Node
    {
        $node = $parentId === null
            ? $this->database->prepare('INSERT INTO node (uuid, `text`, `description`, mindmap_id, parent_id) VALUES (:uuid, :text, :description, :mindmapId, null)')
            : $this->database->prepare('INSERT INTO node (uuid, `text`, `description`, mindmap_id, parent_id) VALUES (:uuid, :text, :description, :mindmapId, :parentId)');
        $node->execute(array_filter([
            ':uuid' => Uuid::uuid4()->toString(),
            ':text' => $text,
            ':description' => $description,
            ':mindmapId' => $mindmapId,
            ':parentId' => $parentId,
        ]));
        $id = $this->database->lastInsertId();
        return $this->get($id);
    }
}
