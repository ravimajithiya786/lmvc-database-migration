<?php

namespace Regur\LMVC\Framework\Database\SchemaUI;

use Regur\LMVC\Framework\Database\Services\MigrationLockService;

class MigrationSchemaWriter
{
    private string $path;
    private MigrationLockService $lock;

    public function __construct(string $path, MigrationLockService $lock)
    {
        $this->path = rtrim($path, '/');
        $this->lock = $lock;
    }

    public function save(string $migrationName, array $schema): void
    {
        $this->lock->assertUnlocked($migrationName);

        file_put_contents(
            "{$this->path}/{$migrationName}.json",
            json_encode($schema, JSON_PRETTY_PRINT)
        );
    }
}
