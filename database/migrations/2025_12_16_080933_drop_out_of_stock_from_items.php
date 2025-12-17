<?php

use Regur\LMVC\Framework\Database\Core\Migration;
use Regur\LMVC\Framework\Database\Core\Schema;
use Regur\LMVC\Framework\Database\Core\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('out_of_stock');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('out_of_stock')->default(false);
        });
    }
};