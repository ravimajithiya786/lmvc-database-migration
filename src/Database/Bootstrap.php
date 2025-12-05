<?php

namespace Regur\LMVC\Framework\Database;

use Symfony\Component\Console\Application;
use Regur\LMVC\Framework\Cli\{MakeMigrationCommand, MigrateCommand, MakeRawMigrationCommand};
use Regur\LMVC\Framework\Database\Core\{DB, Schema};

class Bootstrap
{
    public static function init($config = [])
    {

        // Get DB instance
        $db = new DB([
            'host' =>  $config['host'],  
            'database' => $config['database'],  
            'username' =>  $config['username'], 
            'password' => $config['password']  
        ]);

        // Get connection instance
        $connection = $db->getConnection();

        // Set connection to schema
        Schema::setConnection($connection);

        // Create console application
        $application = new Application();

        // Register commands
        $application->add(new MakeMigrationCommand());
        $application->add(new MakeRawMigrationCommand());
        $application->add(new MigrateCommand($connection));

        // Run CLI application
        $application->run();
    }
}
