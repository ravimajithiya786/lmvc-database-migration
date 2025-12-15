<?php

namespace Regur\LMVC\Framework\Database\Core;

abstract class Seeder
{
    protected \PDO $pdo;

    public function setConnection(\PDO $pdo): void
    {
        $this->pdo = $pdo;
    }

    abstract public function run(): void;

    /* ============================================================
        Helper methods (insert, update, delete, truncate)
    ============================================================ */

    protected function insert(string $table, array $rows): void
    {
        if (empty($rows)) {
            return;
        }

        // Normalize single row
        if (!isset($rows[0])) {
            $rows = [$rows];
        }

        $columns = array_keys($rows[0]);
        $placeholders = '(' . implode(',', array_fill(0, count($columns), '?')) . ')';
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES %s',
            $table,
            implode(',', $columns),
            implode(',', array_fill(0, count($rows), $placeholders))
        );

        $bindings = [];
        foreach ($rows as $row) {
            foreach ($columns as $column) {
                $bindings[] = $row[$column] ?? null;
            }
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
    }
    
    protected function update(string $table, array $data, array $where): void
    {
        if (empty($data)) {
            return;
        }

        if (empty($where)) {
            throw new \Exception('Update without where is not allowed.');
        }

        $sets = [];
        $bindings = [];

        foreach ($data as $column => $value) {
            $sets[] = "{$column} = ?";
            $bindings[] = $value;
        }

        $conditions = [];
        foreach ($where as $column => $value) {
            $conditions[] = "{$column} = ?";
            $bindings[] = $value;
        }

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s',
            $table,
            implode(', ', $sets),
            implode(' AND ', $conditions)
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
    }

    protected function delete(string $table, array $where): void
    {
        $conditions = [];
        $bindings = [];

        if (empty($where)) {
            throw new \Exception('Delete without where is not allowed.');
        }

        foreach ($where as $column => $value) {
            $conditions[] = "{$column} = ?";
            $bindings[] = $value;
        }

        $sql = sprintf(
            'DELETE FROM %s WHERE %s',
            $table,
            implode(' AND ', $conditions)
        );

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($bindings);
    }

    protected function truncate(string $table): void
    {
        $this->pdo->exec("TRUNCATE TABLE {$table}");
    }
}
