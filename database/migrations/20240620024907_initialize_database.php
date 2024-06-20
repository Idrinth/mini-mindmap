<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitializeDatabase extends AbstractMigration
{
    public function change(): void
    {
        $this->table('customer')
            ->addColumn('id', 'int')
            ->addColumn('name', 'string')
            ->addTimestamps()
            ->create();
        $this->table('user')
            ->addColumn('id', 'int')
            ->addColumn('nickname', 'string')
            ->addColumn('email', 'string')
            ->addTimestamps()
            ->create();
        $this->table('customer_user')
            ->addColumn('customer_id', 'int')
            ->addColumn('user_id', 'int')
            ->addColumn('nickname', 'string')
            ->addColumn('role', 'string')
            ->addTimestamps()
            ->addForeignKey('customer_id', 'customer', 'id')
            ->addForeignKey('user_id', 'user', 'id')
            ->create();
        $this->table('mindmap')
            ->addColumn('id', 'int')
            ->addColumn('uuid', 'string')
            ->addColumn('customer_id', 'int')
            ->addColumn('root_element_id', 'int')
            ->addColumn('title', 'string')
            ->addTimestamps()
            ->addIndex('uuid')
            ->addForeignKey('customer_id', 'customer', 'id')
            ->create();
        $this->table('mindmap_user')
            ->addColumn('mindmap_id', 'int')
            ->addColumn('user_id', 'int')
            ->addColumn('role', 'string')
            ->addTimestamps()
            ->addForeignKey('mindmap_id', 'mindmap', 'id')
            ->addForeignKey('user_id', 'user', 'id')
            ->create();
        $this->table('node')
            ->addColumn('id', 'int')
            ->addColumn('uuid', 'string')
            ->addColumn('parent_id', 'int')
            ->addColumn('title', 'string')
            ->addColumn('uuid')
            ->addColumn('parent_id')
            ->addTimestamps()
            ->addIndex('uuid')
            ->addForeignKey('parent_id', 'node', 'id')
            ->create();
        $this->table('event')
            ->addColumn('id', 'int')
            ->addColumn('uuid', 'string')
            ->addColumn('customer_id', 'int')
            ->addColumn('mindmap_id', 'int')
            ->addColumn('node_id', 'int')
            ->addColumn('diff', 'string')
            ->addTimestamps()
            ->addForeignKey('customer_id', 'customer', 'id')
            ->addForeignKey('mindmap_id', 'mindmap', 'id')
            ->addForeignKey('node_id', 'node', 'id')
            ->create();
    }
}
