<?php

namespace Regur\LMVC\Framework\Database;

use Symfony\Component\Console\Application;
use Regur\LMVC\Framework\Cli\{InstallCommand, MakeMigrationCommand, MigrateCommand, MakeRawMigrationCommand, MakeSeederCommand, SeedCommand};
use Regur\LMVC\Framework\Database\Core\{DB, Schema};

class Bootstrap
{
    public static function init($config = [])
    {

        // Get DB instance
        $db = new DB([
            'driver' => $config['driver'],
            'host' =>  $config['host'],  
            'database' => $config['database'],  
            'username' =>  $config['username'], 
            'password' => $config['password'],
            'port' => $config['port']
        ]);

        // Get connection instance
        $connection = $db->getConnection();

        // Set connection to schema
        Schema::setConnection($connection);

        // Create console application
        $application = new Application();

        // Register commands

        // Install command 
        $application->add(new InstallCommand());

        // Migration commands
        $application->add(new MakeMigrationCommand());
        $application->add(new MakeRawMigrationCommand());
        $application->add(new MigrateCommand($connection));

        // Seed commands
        $application->add(new MakeSeederCommand());
        $application->add(new SeedCommand($connection));

        // Run CLI application
        $application->run();
    }
}
