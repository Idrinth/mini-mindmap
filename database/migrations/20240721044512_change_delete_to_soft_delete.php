<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class InitializeDatabase extends AbstractMigration
{
    public function change(): void
    {
        $this->table('mindmap')
            ->addColumn('deleted', 'boolean', ['default' => false])
            ->save();
        $this->table('node')
            ->addColumn('deleted', 'boolean', ['default' => false])
            ->save();
    }
}
