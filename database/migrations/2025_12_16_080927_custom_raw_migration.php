<?php

use Regur\LMVC\Framework\Database\Core\Migration;
use Regur\LMVC\Framework\Database\Core\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $sql = "CREATE TABLE old_orders (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            order_number VARCHAR(50) NOT NULL,
            customer_id INT UNSIGNED NOT NULL,
            total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            status VARCHAR(20) NOT NULL,
            createdAt DATETIME NULL,
            updatedAt DATETIME NULL
        );";
        Schema::execute($sql);
    }

    public function down(): void
    {
        // Reverse your raw SQL here
        // Example:
        // Schema::execute("ALTER TABLE products DROP COLUMN count");
    }
};