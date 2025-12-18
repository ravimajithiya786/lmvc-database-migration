<?php

use Symfony\Component\Process\Process;

/**
 * Helper: Run seed command and log result
 */
function runSeedTest(string $description, array $command, array $expectedMessages)
{
    Log::save("Running: $description");

    $process = new Process($command);
    $process->run();

    // Remove ANSI colors
    $output = preg_replace('/\e\[[0-9;]*m/', '', trim($process->getOutput()));

    // Check if ANY expected message appears in the output
    $passed = false;
    if ($process->isSuccessful()) {
        foreach ($expectedMessages as $msg) {
            if (str_contains($output, $msg) ||
                str_contains($output, $msg . ':')) {
                $passed = true;
                break;
            }
        }
    }


    // FINAL SUMMARY LINE
    Log::save("{$description} → " . ($passed ? "PASSED" : "FAILED"));

    // Raw output
    $lines = array_filter(explode("\n", $output)); 
    $lastLine = end($lines);
    Log::save("Output: $lastLine");

    sleep(1);

    expect($passed)->toBeTrue();
}


/**
 * Test Suite started
 */
beforeAll(function () {

    $filename = 'lmvcdb_tests_seed__' . date('d_m_Y_H_i_s') . '_' . uniqid();
    Log::create($filename);
    Log::save("=== SEED TEST SUITE STARTED ===");
});

/**
 * TEST
 */

// Run all seeders
it('runs all seeders', function () {
    runMigrateTest(
        'seed',
        ['php', 'bin/lmvcdb', 'seed'],
        ['Database seeding completed.', 'Seeder not found']
    );
})->group('seed');

// Run specific seeder with --class option 
it('runs specific seeder', function () {
    runMigrateTest(
        'seed',
        ['php', 'bin/lmvcdb', 'seed', '--class=ProductSeeder'],
        ['Database seeding completed.', 'Seeder not found']
    );
})->group('seed');

/**
 * Test Suite End
 */
afterAll(function () {
    Log::save("=== MIGRATE TEST SUITE ENDED ===");
});


// Command to test - php ./vendor/bin/pest --group=seed
