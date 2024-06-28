<?php

namespace De\Idrinth\MiniMindmap\Repository;

use De\Idrinth\MiniMindmap\NotFoundException;
use PDO;
use Ramsey\Uuid\Uuid;

class Node
{
    public function  __construct(private PDO $database)
    {}
    public function count()
    {
        $mindmap = $this->database->prepare('SELECT Count(id) FROM node');
        $mindmap->execute([]);
        return (int) $mindmap->fetchColumn();
    }
    public function changed(int $mindmap, string $since): array
    {
        $statement = $this->database->prepare('SELECT id FROM node WHERE mindmap_id=:mindmap AND updated_at >= :since');
        $statement->execute([
            ':since' => $since,
            ':mindmap' => $mindmap,
        ]);
        $result = [];
        foreach ($statement->fetchAll(PDO::FETCH_OBJ) as $node) {
            $result[] = $this->get($node->id);
        }
        return array_map(function(\De\Idrinth\MiniMindmap\Entity\Node $node) {
            if ($node->parentId !== null) {
                $node->parentUuid = $this->idToUuid($node->parentId);
            }
            return $node;
        }, $result);
    }
    public function uuidToId(int $mindmap, string $uuid): int
    {
        $node = $this->database->prepare('SELECT id FROM node WHERE uuid=:uuid and mindmap_id=:mindmap');
        $node->execute([
            ':uuid' => $uuid,
            ':mindmap' => $mindmap,
        ]);
        return (int) $node->fetchColumn();
    }
    public function idToUuid(int $id): string
    {
        $node = $this->database->prepare('SELECT uuid FROM node WHERE id=:id');
        $node->execute([
            ':id' => $id,
        ]);
        return (string) $node->fetchColumn();
    }
    public function get(int $id): \De\Idrinth\MiniMindmap\Entity\Node
    {
        $node = $this->database->prepare('SELECT mindmap_id as mindmapId,id,uuid,parent_id AS parentId,`text`,description FROM node WHERE id=:id');
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
        $statement = $this->database->prepare('UPDATE node SET `text`=:text, description=:description, updated_at=:now WHERE id=:id');
        $statement->execute([
            'id' => $id,
            'text' => $patch['text'] ?? $node->text,
            'now' => date('Y-m-d H:i:s'),
            'description' => $patch['description'] ?? $node->description,
        ]);
        if ($node->parentId === null) {
            (new Mindmap($this->database, $this))->update($node->mindmapId, $patch['text'] ?? $node->text);
        }
        return $this->get($id);
    }
    public function delete(int $id): void
    {
        foreach ($this->getChildren($id) as $child) {
            $this->delete($child->id);
        }
        $this->database->exec("DELETE FROM node WHERE id=$id");
    }
    public function create(string $text, string $description, int $mindmapId, ?int $parentId = null): \De\Idrinth\MiniMindmap\Entity\Node
    {
        $node = $parentId === null
            ? $this->database->prepare('INSERT INTO node (uuid, `text`, `description`, mindmap_id, parent_id, updated_at) VALUES (:uuid, :text, :description, :mindmapId, null, :now)')
            : $this->database->prepare('INSERT INTO node (uuid, `text`, `description`, mindmap_id, parent_id, updated_at) VALUES (:uuid, :text, :description, :mindmapId, :parentId, :now)');
        $node->execute(array_filter([
            ':uuid' => Uuid::uuid4()->toString(),
            ':text' => $text,
            ':description' => $description,
            ':mindmapId' => $mindmapId,
            ':parentId' => $parentId,
            ':now' => date('Y-m-d H:i:s'),
        ], function($entry) {
            return $entry !== null;
        }));
        $id = $this->database->lastInsertId();
        return $this->get($id);
    }
}
