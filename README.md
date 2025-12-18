<p align="center">
  <img src="https://www.regur.net/_next/static/media/logo.87cbe41e.svg" alt="Regur Technologies Logo" width="180">
</p>

# Regur Technologies - LMVC DB Migration Library

A simple database migration package for PHP applications, inspired by Laravel's Artisan.

---

## 📌 Features
- Create and manage database migrations
- CLI command similar to Laravel Artisan
- Auto-generates migration files
- Supports custom database schemas

---

## 🚀 Installation

Install via Composer:

```bash
composer require regur/lmvc-database-migration
```

After installation, the CLI command will be available at:

```bash
php lmvcdb
```

Important - the .env file is also needed for database credentials

below of lmvcdb file (same like atrisan file)

```bash

<?php

namespace Regur\LMVC\Framework\Bin;

require './vendor/autoload.php';

use Regur\LMVC\Framework\Database\Bootstrap;
use Regur\LMVC\Framework\Database\Libs\Dotenv;

$dotenv = Dotenv::createImmutable(getcwd());
$dotenv->load();

$dbcred = [
    'host' => $_ENV['DB_HOST'],
    'driver'=> $_ENV['DB_DRIVER'],
    'database' => $_ENV['DB_NAME'],
    'username' => $_ENV['DB_UNAME'],
    'password' => $_ENV['DB_PWD'],
    'port' => $_ENV['DB_PORT']
];

Bootstrap::init($dbcred);

?>

```

Now you can run:

```bash
php lmvcdb migrate
```

---

## 🛠 Usage

### Create a New Migration
```bash
php lmvcdb make:migration create_users_table
```
This will generate a migration file inside `database/migrations`.

### Run Migrations
```bash
php lmvcdb migrate
```
Applies all pending migrations.

### Fresh Migrations (Reset & Run All)
```bash
php lmvcdb migrate:fresh
```
Drops all tables and runs migrations from scratch.

### Apply Pending Migrations
```bash
php lmvcdb migrate:up
```
Runs the next batch of pending migrations.

### Rollback the Last Migration Batch
```bash
php lmvcdb migrate:down
```
Rolls back the last executed migration batch.

### Refresh Migrations (Rollback & Reapply)
```bash
php lmvcdb migrate:refresh
```
Rolls back all migrations and runs them again.

---

### Make Seeder 
```bash
php lmvcdb make:seeder UsersSeeder
```
It will create new seeder

### Run all seeders
```bash
php lmvcdb seed
```
It will run all seeders at once

### Run specific seeder
```bash
php lmvcdb seed --class=UsersSeeder
```
It will run specific seeder 

---

## 📂 Project Structure
```
lmvc-database-migration/
│── src/
│   ├── Database/
│   │   ├── Core/                                # Core migration classes
│   │   │   ├── Migration.php
│   │   │   ├── Schema.php
│   │   │   ├── Blueprint.php
│   │   │   ├── DB.php
│   │   ├── Libs/                                # Relevent custom libraries
│   │   │   ├── Dotenv.php
│   ├── Composer/
│   │   ├── Installer.php                        # This will create a file lmvc at root of your project
│   ├── Cli/                                     # This contains commands registered in symfony CLI
│   │   ├── InstallCommand.php                   # php vendor/cli/lmvcdb install 
│   │   ├── MakeMigrationCommand.php             # php lmvcdb make:migration <args>
│   │   ├── MakeRawMigrationCommand.php          # php lmvcdb make:raw-migration <args>
│   │   ├── MigrateCommand.php                   # php lmvcdb migrate <args>
│   │   ├── MakeSeederCommand.php                # php lmvcdb make:seeder <args>
│   │   ├── SeedCommand.php                      # php lmvcdb seed <args>
│   ├── Bootstrap.php                            # Main bootstrap file
│── bin/
│   ├── lmvcdb                                   # CLI command file
│── composer.json                                # Composer JSON file (for versioning and maintaining relevent packages)
│── README.md
```

---

## 🔧 Configuration

Configure the database connection in `lmvcdb`:

```php
Regur\LMVC\Framework\Database\Bootstrap::init([
    'host' => $_ENV['DB_HOST'],
    'driver'=> $_ENV['DB_DRIVER'],
    'database' => $_ENV['DB_NAME'],
    'username' => $_ENV['DB_UNAME'],
    'password' => $_ENV['DB_PWD'],
    'port' => $_ENV['DB_PORT']
]);
```

For docker you need to run commands under docker container 

```bash
docker exec -it pinlocal "your desired command"
```

---


## 📜 License
This package is open-source and available under the MIT License.

💡 **Need help?** Feel free to create an issue or contribute!
