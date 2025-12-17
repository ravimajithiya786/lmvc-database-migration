<?php

use Regur\LMVC\Framework\Database\Core\Migration;
use Regur\LMVC\Framework\Database\Core\Schema;
use Regur\LMVC\Framework\Database\Core\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('old_orders');

    }

    public function down(): void
    {
        Schema::create('old_orders', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

    }
};