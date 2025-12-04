<?php

namespace Regur\LMVC\Framework\Database\Core;

class Blueprint
{
    protected string $table;

    /** @var string[] List of column definitions */
    protected array $columns = [];

    /** @var array<int,string> Default values mapped by column index */
    protected array $defaults = [];

    /** @var string[] Columns to drop */
    protected array $drops = [];

    /** @var array<string,string> Columns to rename [old => new] */
    protected array $renames = [];

    /** @var array<int,string> Columns to modify/change */
    protected array $changes = [];

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    // -------------------------------
    // Column Types
    // -------------------------------

    public function id(): self
    {
        $this->columns[] = "`id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY";
        return $this;
    }

    public function string(string $column, int $length = 255, bool $nullable = true): self
    {
        $this->columns[] = "`$column` VARCHAR($length)" . ($nullable ? " NULL" : " NOT NULL");
        return $this;
    }

    public function text(string $column, bool $nullable = true): self
    {
        $this->columns[] = "`$column` TEXT" . ($nullable ? " NULL" : " NOT NULL");
        return $this;
    }

    public function longText(string $column): self
    {
        $this->columns[] = "`$column` LONGTEXT NOT NULL";
        return $this;
    }

    public function enum(string $column, array $values): self
    {
        $vals = implode(',', array_map(fn($v) => "'$v'", $values));
        $this->columns[] = "`$column` ENUM($vals) NOT NULL";
        return $this;
    }

    public function integer(string $column, int $length = 11): self
    {
        $this->columns[] = "`$column` INT($length) NOT NULL";
        return $this;
    }

    public function bigInteger(string $column, int $length = 20): self
    {
        $this->columns[] = "`$column` BIGINT($length) NOT NULL";
        return $this;
    }

    public function float(string $column, int $total = 8, int $places = 2): self
    {
        $this->columns[] = "`$column` FLOAT($total,$places) NOT NULL";
        return $this;
    }

    public function double(string $column, int $total = 8, int $places = 2): self
    {
        $this->columns[] = "`$column` DOUBLE($total,$places) NOT NULL";
        return $this;
    }

    public function decimal(string $column, int $total = 8, int $places = 2): self
    {
        $this->columns[] = "`$column` DECIMAL($total,$places) NOT NULL";
        return $this;
    }

    public function boolean(string $column): self
    {
        $this->columns[] = "`$column` TINYINT(1) NOT NULL";
        return $this;
    }

    public function date(string $column): self
    {
        $this->columns[] = "`$column` DATE NOT NULL";
        return $this;
    }

    public function dateTime(string $column): self
    {
        $this->columns[] = "`$column` DATETIME NOT NULL";
        return $this;
    }

    public function time(string $column): self
    {
        $this->columns[] = "`$column` TIME NOT NULL";
        return $this;
    }

    public function timestamp(string $column): self
    {
        $this->columns[] = "`$column` TIMESTAMP NOT NULL";
        return $this;
    }

    public function binary(string $column): self
    {
        $this->columns[] = "`$column` BLOB NOT NULL";
        return $this;
    }

    public function json(string $column): self
    {
        $this->columns[] = "`$column` JSON NOT NULL";
        return $this;
    }

    public function timestamps(): self
    {
        $this->columns[] = "`created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
        $this->columns[] = "`updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
        return $this;
    }

    // -------------------------------
    // Modifiers
    // -------------------------------

    public function nullable(): self
    {
        $last = $this->getLastColumnIndex();
        if ($last === null) return $this;

        $col = $this->columns[$last];

        if (strpos($col, 'NOT NULL') !== false) {
            $col = str_replace('NOT NULL', 'NULL', $col);
        } elseif (strpos($col, 'NULL') === false) {
            $col .= " NULL";
        }

        $this->columns[$last] = $col;
        return $this;
    }

    public function default(mixed $value): self
    {
        $last = $this->getLastColumnIndex();
        if ($last === null) return $this;

        if (is_string($value)) {
            $value = "'$value'";
        } elseif (is_bool($value)) {
            $value = $value ? 1 : 0;
        } elseif ($value === null) {
            $value = "NULL";
        }

        $this->defaults[$last] = $value;
        return $this;
    }

    // -------------------------------
    // Drop / Rename / Change Columns
    // -------------------------------

    public function dropColumn(string $column): self
    {
        $this->drops[] = $column;
        return $this;
    }

    public function renameColumn(string $from, string $to): self
    {
        $this->renames[$from] = $to;
        return $this;
    }

    public function changeColumn(string $column): self
    {
        $this->changes[] = $column;
        return $this;
    }

    // -------------------------------
    // SQL Builders
    // -------------------------------

    public function toSql(): string
    {
        $sql = [];
        foreach ($this->columns as $i => $col) {
            if (isset($this->defaults[$i])) {
                $col .= " DEFAULT " . $this->defaults[$i];
            }
            $sql[] = $col;
        }
        return "CREATE TABLE `$this->table` (" . implode(", ", $sql) . ") ENGINE=InnoDB;";
    }

    public function toAlterSql(): string
    {
        $alter = [];

        // Add/modify columns
        foreach ($this->columns as $i => $col) {
            if (isset($this->defaults[$i])) {
                $col .= " DEFAULT " . $this->defaults[$i];
            }
            if (in_array($i, $this->changes, true)) {
                $alter[] = "MODIFY COLUMN $col";
            } else {
                $alter[] = "ADD COLUMN $col";
            }
        }

        // Drop columns
        foreach ($this->drops as $col) {
            $alter[] = "DROP COLUMN `$col`";
        }

        // Rename columns
        foreach ($this->renames as $from => $to) {
            $alter[] = "RENAME COLUMN `$from` TO `$to`";
        }

        return implode(", ", $alter);
    }

    // -------------------------------
    // Helpers
    // -------------------------------

    protected function getLastColumnIndex(): ?int
    {
        return array_key_last($this->columns);
    }
}
