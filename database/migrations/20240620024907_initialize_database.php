<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitializeDatabase extends AbstractMigration
{
    public function change(): void
    {
        $this->table('customer')
            ->addColumn('name', 'string')
            ->addTimestamps()
            ->create();
        $this->table('user')
            ->addColumn('nickname', 'string')
            ->addColumn('email', 'string')
            ->addTimestamps()
            ->create();
        $this->table('customer_user')
            ->addColumn('customer_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('nickname', 'string')
            ->addColumn('role', 'string')
            ->addTimestamps()
            ->addForeignKey('customer_id', 'customer', 'id')
            ->addForeignKey('user_id', 'user', 'id')
            ->create();
        $this->table('mindmap')
            ->addColumn('uuid', 'string')
            ->addColumn('customer_id', 'integer')
            ->addColumn('root_element_id', 'integer')
            ->addColumn('title', 'string')
            ->addTimestamps()
            ->addIndex('uuid')
            ->addForeignKey('customer_id', 'customer', 'id')
            ->create();
        $this->table('mindmap_user')
            ->addColumn('mindmap_id', 'integer')
            ->addColumn('user_id', 'integer')
            ->addColumn('role', 'string')
            ->addTimestamps()
            ->addForeignKey('mindmap_id', 'mindmap', 'id')
            ->addForeignKey('user_id', 'user', 'id')
            ->create();
        $this->table('node')
            ->addColumn('uuid', 'string')
            ->addColumn('parent_id', 'integer')
            ->addColumn('mindmap_id', 'integer')
            ->addColumn('text', 'string')
            ->addColumn('description', 'string')
            ->addTimestamps()
            ->addIndex('uuid')
            ->addForeignKey('parent_id', 'node', 'id')
            ->addForeignKey('mindmap_id', 'mindmap', 'id')
            ->create();
    }
}
