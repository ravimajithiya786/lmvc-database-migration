<?php

use Regur\LMVC\Framework\Database\Core\Migration;
use Regur\LMVC\Framework\Database\Core\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Write your raw SQL here
        // Example:
        // Schema::execute("ALTER TABLE products ADD COLUMN count INT DEFAULT 0");
    }

    public function down(): void
    {
        // Reverse your raw SQL here
        // Example:
        // Schema::execute("ALTER TABLE products DROP COLUMN count");
    }
};