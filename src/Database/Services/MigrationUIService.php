<?php

namespace Regur\LMVC\Framework\Database\Services;

use Regur\LMVC\Framework\Database\SchemaUI\MigrationSchemaReader;

class MigrationUIService
{
    private MigrationLockService $lock;
    private MigrationSchemaReader $reader;

    public function __construct(
        MigrationLockService $lock,
        MigrationSchemaReader $reader
    ) {
        $this->lock = $lock;
        $this->reader = $reader;
    }

    public function get(string $migrationName): array
    {
        $locked = $this->lock->isLocked($migrationName);

        return [
            'migration' => $migrationName,
            'locked'    => $locked,
            'editable'  => ! $locked,
            'schema'    => $this->reader->read($migrationName),
        ];
    }
}
