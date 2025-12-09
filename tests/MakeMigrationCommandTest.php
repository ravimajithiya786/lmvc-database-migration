<?php

use Symfony\Component\Process\Process;

require 'Log.php';

/** Test Started */
beforeAll(function () {
    // Unique log filename: migration_tests_14_01_2025_13_55_02_a1b2c3d4
    $filename = 'migration_tests_' . date('d_m_Y_H_i_s') . '_' . uniqid();
    Log::create($filename);
    Log::save("=== TEST SUITE STARTED ===");
});

/** 1. Create a new table */
it('creates products table', function () {
    Log::save("Running: creates products table");

    $process = new Process(['php', 'bin/lmvcdb', 'make:migration', 'create_products_table']);
    $process->run();

    Log::save("Output: " . trim($process->getOutput()));

    expect($process->isSuccessful())->toBeTrue();
    expect($process->getOutput())->toContain('Migration created:');
});

/** 2. Add a column to an existing table */
it('adds category_id column to products table', function () {
    Log::save("Running: adds category_id column to products table");

    $process = new Process(['php', 'bin/lmvcdb', 'make:migration', 'add_column_category_id_to_products', '--table=products']);
    $process->run();

    Log::save("Output: " . trim($process->getOutput()));

    expect($process->isSuccessful())->toBeTrue();
    expect($process->getOutput())->toContain('Migration created:');
});

/** 3. Drop a column */
it('drops description column from products table', function () {
    Log::save("Running: drops description column from products table");

    $process = new Process(['php', 'bin/lmvcdb', 'make:migration', 'drop_column_description_from_products', '--table=products']);
    $process->run();

    Log::save("Output: " . trim($process->getOutput()));

    expect($process->isSuccessful())->toBeTrue();
    expect($process->getOutput())->toContain('Migration created:');
});

/** 4. Rename a column */
it('renames name column to title in products table', function () {
    Log::save("Running: renames name column to title");

    $process = new Process(['php', 'bin/lmvcdb', 'make:migration', 'rename_name_to_title_in_products', '--table=products']);
    $process->run();

    Log::save("Output: " . trim($process->getOutput()));

    expect($process->isSuccessful())->toBeTrue();
    expect($process->getOutput())->toContain('Migration created:');
});

/** 5. Add multiple columns */
it('adds price and stock columns to products table', function () {
    Log::save("Running: adds price and stock columns to products table");

    $process = new Process(['php', 'bin/lmvcdb', 'make:migration', 'add_price_and_stock_to_products', '--table=products']);
    $process->run();

    Log::save("Output: " . trim($process->getOutput()));

    expect($process->isSuccessful())->toBeTrue();
    expect($process->getOutput())->toContain('Migration created:');
});

/** 6. Drop a table */
it('drops old_orders table', function () {
    Log::save("Running: drops old_orders table");

    $process = new Process(['php', 'bin/lmvcdb', 'make:migration', 'drop_old_orders_table']);
    $process->run();

    Log::save("Output: " . trim($process->getOutput()));

    expect($process->isSuccessful())->toBeTrue();
    expect($process->getOutput())->toContain('Migration created:');
});

/** 7. Raw migration */
it('creates raw migration', function () {
    Log::save("Running: creates raw migration");

    $process = new Process(['php', 'bin/lmvcdb', 'make:raw-migration', 'custom_raw_migration']);
    $process->run();

    Log::save("Output: " . trim($process->getOutput()));

    expect($process->isSuccessful())->toBeTrue();
    expect($process->getOutput())->toContain('Migration created:');
});

/** 8. Rename table */
it('renames products table to items', function () {
    Log::save("Running: renames products table to items");

    $process = new Process(['php', 'bin/lmvcdb', 'make:migration', 'rename_products_to_items']);
    $process->run();

    Log::save("Output: " . trim($process->getOutput()));

    expect($process->isSuccessful())->toBeTrue();
    expect($process->getOutput())->toContain('Migration created:');
});

/** 9. Add boolean column */
it('adds is_active column to users table', function () {
    Log::save("Running: adds is_active column to users table");

    $process = new Process(['php', 'bin/lmvcdb', 'make:migration', 'add_is_active_to_users', '--table=users']);
    $process->run();

    Log::save("Output: " . trim($process->getOutput()));

    expect($process->isSuccessful())->toBeTrue();
    expect($process->getOutput())->toContain('Migration created:');
});

/** 10. Drop boolean column */
it('drops is_verified column from users table', function () {
    Log::save("Running: drops is_verified column from users table");

    $process = new Process(['php', 'bin/lmvcdb', 'make:migration', 'drop_is_verified_from_users', '--table=users']);
    $process->run();

    Log::save("Output: " . trim($process->getOutput()));

    expect($process->isSuccessful())->toBeTrue();
    expect($process->getOutput())->toContain('Migration created:');
});

/** Test ended */
afterAll(function () {
    Log::save("=== TEST SUITE ENDED ===");
});
