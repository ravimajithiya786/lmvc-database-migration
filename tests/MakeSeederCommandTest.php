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

    $passed = $process->isSuccessful() && str_contains($output, 'Seeder created:');

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
    if (is_dir('database/seeders')) {
        $files = glob('database/seeders/*.php');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    $filename = 'lmvcdb_tests_makeseeder__' . date('d_m_Y_H_i_s') . '_' . uniqid();
    Log::create($filename);
    Log::save("=== TEST SUITE STARTED ===");
});

/**
 * TESTS
 */
it('create products seeder', function () {
    runMigrationTest(
        'creates products seeder',
        ['php', 'bin/lmvcdb', 'make:seeder', 'ProductSeeder']
    );
})->group('makeseeder');


it('create vendors seeder', function () {
    runMigrationTest(
        'creates vendors seeder',
        ['php', 'bin/lmvcdb', 'make:seeder', 'VendorSeeder']
    );
})->group('makeseeder');

/**
 * Test Suite End
 */
afterAll(function () {
    Log::save("=== TEST SUITE ENDED ===");
});

// Command to test - php ./vendor/bin/pest --group=makeseeder
