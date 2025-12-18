<?php

namespace Regur\LMVC\Framework\Database\Services;

use PDO;
use Regur\LMVC\Framework\Database\Exceptions\MigrationLockedException;

class MigrationLockService
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function isLocked(string $migrationName): bool
    {
        $stmt = $this->pdo->prepare(
            "SELECT COUNT(*) FROM migrations WHERE migrationName = :name"
        );
        $stmt->execute(['name' => $migrationName]);

        return (bool) $stmt->fetchColumn();
    }

    public function assertUnlocked(string $migrationName): void
    {
        if ($this->isLocked($migrationName)) {
            throw new MigrationLockedException(
                "Migration '{$migrationName}' is already migrated and locked."
            );
        }
    }
}
