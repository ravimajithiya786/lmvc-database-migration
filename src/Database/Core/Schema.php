<?php

namespace Regur\LMVC\Framework\Database\Core;

use Regur\LMVC\Framework\Database\Core\Blueprint;

class Schema
{
    protected static ?\PDO $pdo = null;

    public static function setConnection(\PDO $pdo)
    {
        self::$pdo = $pdo;
    }

    // Table Operations
    public static function create(string $table, callable $callback)
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        $query = $blueprint->toSql();
        self::execute($query);
    }
    
    public static function table(string $table, callable $callback)
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        $query = "ALTER TABLE `$table` " . $blueprint->toAlterSql();
        self::execute($query);
    }

    public static function dropIfExists(string $table)
    {
        self::execute("DROP TABLE IF EXISTS `$table`");
    }

    public static function dropAllTables()
    {
        $stmt = self::$pdo->query("SHOW TABLES");
        $tables = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        foreach ($tables as $table) {
            self::dropIfExists($table);
        }
    }

    public static function alterTable(string $table, callable $callback)
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);
        $query = "ALTER TABLE `$table` " . $blueprint->toAlterSql();
        self::execute($query);
    }

    public static function renameTable(string $from, string $to)
    {
        self::execute("RENAME TABLE `$from` TO `$to`");
    }

    public static function truncateTable(string $table)
    {
        self::execute("TRUNCATE TABLE `$table`");
    }

    // Data Operations
    public static function insert(string $table, array $data)
    {
        $columns = implode('`, `', array_keys($data));
        $values = implode("', '", array_values($data));
        self::execute("INSERT INTO `$table` (`$columns`) VALUES ('$values')");
    }

    public static function update(string $table, array $data, string $where)
    {
        $set = [];
        foreach ($data as $column => $value) {
            $set[] = "`$column` = '$value'";
        }
        $setClause = implode(', ', $set);
        self::execute("UPDATE `$table` SET $setClause WHERE $where");
    }

    public static function delete(string $table, string $where)
    {
        self::execute("DELETE FROM `$table` WHERE $where");
    }

    public static function copyTable(string $source, string $destination)
    {
        self::execute("CREATE TABLE `$destination` LIKE `$source`");
        self::execute("INSERT INTO `$destination` SELECT * FROM `$source`");
    }

    // Utility Methods
    protected static function execute(string $query)
    {
        if (!self::$pdo) {
            throw new \Exception("Database connection not set.");
        }

        try {
            return self::$pdo->exec($query);
        } catch (\PDOException $e) {
            throw new \Exception("Database Error: " . $e->getMessage());
        }
    }

    public static function beginTransaction()
    {
        self::$pdo->beginTransaction();
    }

    public static function commit()
    {
        self::$pdo->commit();
    }

    public static function rollBack()
    {
        self::$pdo->rollBack();
    }
}
