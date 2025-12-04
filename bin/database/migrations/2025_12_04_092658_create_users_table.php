<?php

use Regur\LMVC\Framework\Database\Core\Migration;
use Regur\LMVC\Framework\Database\Core\Schema;
use Regur\LMVC\Framework\Database\Core\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users_new', function (Blueprint $table) {
            $table->id();
            $table->string('username', 150)->default('guest');
            $table->text('bio')->nullable();
            $table->enum('role', ['admin','user','guest'])->default('guest');
            $table->integer('rollno',12)->default(0);
            $table->bigInteger('big_number',20)->default(0);
            $table->float('rating',8,2)->default(0.0);
            $table->double('score',10,3)->default(0.0);
            $table->decimal('balance',10,2)->default(0.0);
            $table->boolean('active')->default(true);
            $table->dateTime('last_login')->nullable();
            $table->timestamps();
        });

        Schema::table('users_new', function (Blueprint $table) {
            $table->string('email',255)->nullable()->default('example@example.com');
            $table->renameColumn('username','display_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_new');
    }
};
