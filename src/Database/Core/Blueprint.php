<?php

namespace Regur\LMVC\Framework\Database\Core;

class Blueprint
{
    protected string $table;
    protected array $columns = [];
    protected array $primaryKeys = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function id()
    {
        $this->columns[] = "`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
    }

    public function string(string $column, int $length = 255, $nullable = true)
    {
        $this->columns[] = !$nullable ? "`$column` VARCHAR($length) NOT NULL" : "`$column` VARCHAR($length) NULL";
    }

    public function text(string $column,  $nullable = true)
    {
        $this->columns[] = !$nullable ? "`$column` TEXT NOT NULL" : "`$column` TEXT NULL";
    }

    public function longText(string $column)
    {
        $this->columns[] = "`$column` LONGTEXT NOT NULL";
    }

    public function enum(string $column, array $values)
    {
        $values = array_map(fn($value) => "'$value'", $values);
        $this->columns[] = "`$column` ENUM(" . implode(',', $values) . ") NOT NULL";
    }

    public function integer(string $column)
    {
        $this->columns[] = "`$column` INT NOT NULL";
    }

    public function bigInteger(string $column)
    {
        $this->columns[] = "`$column` BIGINT NOT NULL";
    }

    public function float(string $column, int $total = 8, int $places = 2)
    {
        $this->columns[] = "`$column` FLOAT($total,$places) NOT NULL";
    }

    public function double(string $column, int $total = 8, int $places = 2)
    {
        $this->columns[] = "`$column` DOUBLE($total,$places) NOT NULL";
    }

    public function decimal(string $column, int $total = 8, int $places = 2)
    {
        $this->columns[] = "`$column` DECIMAL($total,$places) NOT NULL";
    }

    public function boolean(string $column)
    {
        $this->columns[] = "`$column` TINYINT(1) NOT NULL";
    }

    public function date(string $column)
    {
        $this->columns[] = "`$column` DATE NOT NULL";
    }

    public function dateTime(string $column)
    {
        $this->columns[] = "`$column` DATETIME NOT NULL";
    }

    public function time(string $column)
    {
        $this->columns[] = "`$column` TIME NOT NULL";
    }

    public function timestamp(string $column)
    {
        $this->columns[] = "`$column` TIMESTAMP NOT NULL";
    }

    public function binary(string $column)
    {
        $this->columns[] = "`$column` BLOB NOT NULL";
    }

    public function json(string $column)
    {
        $this->columns[] = "`$column` JSON NOT NULL";
    }

    public function timestamps()
    {
        $this->columns[] = "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
    }

    public function nullable()
    {
        
    }

    public function toSql(): string
    {
        return "CREATE TABLE `$this->table` (" . implode(", ", $this->columns) . ") ENGINE=InnoDB;";
    }

    public function toAlterSql(): string
    {
        $alterations = [];
        foreach ($this->columns as $column) {
            $alterations[] = "ADD COLUMN $column";
        }
        return implode(", ", $alterations);
    }
}
