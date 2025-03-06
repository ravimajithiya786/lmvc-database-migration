<?php

namespace Regur\LMVC\Framework\Database\Core;

abstract class Migration
{
    abstract public function up(): void;
    abstract public function down(): void;
}
