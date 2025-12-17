<?php

use Regur\LMVC\Framework\Database\Core\Migration;
use Regur\LMVC\Framework\Database\Core\Schema;
use Regur\LMVC\Framework\Database\Core\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::renameTable('items', 'products');
    }

    public function down(): void
    {
       Schema::renameTable('products', 'items');
    }
};