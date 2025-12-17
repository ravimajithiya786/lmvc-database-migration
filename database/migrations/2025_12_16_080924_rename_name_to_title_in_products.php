<?php

use Regur\LMVC\Framework\Database\Core\Migration;
use Regur\LMVC\Framework\Database\Core\Schema;
use Regur\LMVC\Framework\Database\Core\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add your alterations here
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Reverse your alterations here
        });
    }
};