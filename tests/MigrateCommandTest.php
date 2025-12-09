<?php

use Symfony\Component\Process\Process;

/**
 * Helper: Run migrate command and log result
 */
function runMigrateTest(string $description, array $command, array $expectedMessages)
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

    $filename = 'lmvcdb_tests_migrate__' . date('d_m_Y_H_i_s') . '_' . uniqid();
    Log::create($filename);
    Log::save("=== MIGRATE TEST SUITE STARTED ===");
});

/**
 * TESTS
 */
// it('runs default migrate', function () {
//     runMigrateTest(
//         'default migrate',
//         ['php', 'bin/lmvcdb', 'migrate'],
//         ['All migrations executed successfully', 'Migration executed:', 'No pending migrations found']
//     );
// });

// it('runs migrate with --up', function () {
//     runMigrateTest(
//         'migrate --up',
//         ['php', 'bin/lmvcdb', 'migrate', '--up'],
//         ['All pending migrations executed successfully.']
//     );
// });

// it('runs migrate with --down', function () {
//     runMigrateTest(
//         'migrate --down',
//         ['php', 'bin/lmvcdb', 'migrate', '--down'],
//         ['Droping last batch of migrations.', 'Rolled back last batch']
//     );
// });

// it('runs migrate with --fresh', function () {
//     runMigrateTest(
//         'migrate --fresh',
//         ['php', 'bin/lmvcdb', 'migrate', '--fresh'],
//         ['Dropping all tables..., Migrating all tables...']
//     );
// });

it('runs migrate with --refresh', function () {
    runMigrateTest(
        'migrate --refresh',
        ['php', 'bin/lmvcdb', 'migrate', '--refresh'],
        ['Database all tables refreshed']
    );
});

/**
 * Test Suite End
 */
afterAll(function () {
    Log::save("=== MIGRATE TEST SUITE ENDED ===");
});
