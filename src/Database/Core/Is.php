<?php

namespace Regur\LMVC\Framework\Database\Core;

class Is 
{
    public $pdo;
    public $migrationName;
    public function __construct($pdo, $migrationName)
    {
        $this->pdo = $pdo;
        $this->migrationName = $migrationName;
    }

    public function alreadyMigrated()
    {
        
    }

    public function 
}