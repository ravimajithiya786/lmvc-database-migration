<?php

namespace Regur\LMVC\Framework\Database\SchemaUI;

class MigrationSchemaReader
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = rtrim($path, '/');
    }

    public function read(string $migrationName): array
    {
        $file = "{$this->path}/{$migrationName}.json";

        if (!file_exists($file)) {
            return [];
        }

        return json_decode(file_get_contents($file), true) ?? [];
    }
}
