<?php

use Symfony\Component\Process\Process;

require 'Log.php';

/**
 * Helper: Run migration command and log result
 */
function runMigrationTest(string $description, array $command)
{
    Log::save("Running: $description");

    $process = new Process($command);
    $process->run();

    // Remove ANSI colors (ESC sequences)
    $output = preg_replace('/\e\[[0-9;]*m/', '', trim($process->getOutput()));

    $passed = $process->isSuccessful() && str_contains($output, 'Migration created:');

    // FINAL SUMMARY LINE
    Log::save("{$description} → " . ($passed ? "PASSED" : "FAILED"));

    // ALSO log raw output (optional, comment out if not needed)
    Log::save("Output: $output");

    sleep(1);

    expect($passed)->toBeTrue();
}

/**
 * Test Suite started
 */
beforeAll(function () {
    // Reset migration directory to avoid "already exists" errors
    if (is_dir('database/migrations')) {
        $files = glob('database/migrations/*.php');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    $filename = 'lmvcdb_tests_makemigration__' . date('d_m_Y_H_i_s') . '_' . uniqid();
    Log::create($filename);
    Log::save("=== TEST SUITE STARTED ===");
});

/**
 * TESTS
 */
it('creates products table', function () {
    runMigrationTest(
        'creates products table',
        ['php', 'bin/lmvcdb', 'make:migration', 'create_products_table']
    );
})->group('makemigration');

it('adds category_id column to products table', function () {
    runMigrationTest(
        'adds category_id column to products table',
        ['php', 'bin/lmvcdb', 'make:migration', 'add_column_category_id_to_products', '--table=products']
    );
})->group('makemigration');

it('drops description column from products table', function () {
    runMigrationTest(
        'drops description column from products table',
        ['php', 'bin/lmvcdb', 'make:migration', 'drop_column_description_from_products', '--table=products']
    );
})->group('makemigration');

it('renames name column to title in products table', function () {
    runMigrationTest(
        'renames name column to title in products table',
        ['php', 'bin/lmvcdb', 'make:migration', 'rename_name_to_title_in_products', '--table=products']
    );
})->group('makemigration');

it('adds price and stock columns to products table', function () {
    runMigrationTest(
        'adds price and stock columns to products table',
        ['php', 'bin/lmvcdb', 'make:migration', 'add_price_and_stock_to_products', '--table=products']
    );
})->group('makemigration');

it('drops old_orders table', function () {
    runMigrationTest(
        'drops old_orders table',
        ['php', 'bin/lmvcdb', 'make:migration', 'drop_old_orders_table']
    );
})->group('makemigration');

it('creates raw migration', function () {
    runMigrationTest(
        'creates raw migration',
        ['php', 'bin/lmvcdb', 'make:raw-migration', 'custom_raw_migration', '--table=products']
    );
})->group('makemigration');

it('renames products table to items', function () {
    runMigrationTest(
        'renames products table to items',
        ['php', 'bin/lmvcdb', 'make:migration', 'rename_products_to_items']
    );
})->group('makemigration');

it('adds out_of_stock column to items table', function () {
    runMigrationTest(
        'adds out_of_stock column to items table',
        ['php', 'bin/lmvcdb', 'make:migration', 'add_out_of_stock_to_items', '--table=items']
    );
})->group('makemigration');

it('drops out_of_stock column from items table', function () {
    runMigrationTest(
        'drops out_of_stock column from items table',
        ['php', 'bin/lmvcdb', 'make:migration', 'drop_out_of_stock_from_items', '--table=items']
    );
})->group('makemigration');

/**
 * Test Suite End
 */
afterAll(function () {
    Log::save("=== TEST SUITE ENDED ===");
});

// Command to test - php ./vendor/bin/pest --group=makemigration
