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
composer require regur/
```

After installation, the CLI command will be available at:

```bash
php vendor/bin/command
```

To make it accessible from the root directory, create a symlink:

```bash
ln -s vendor/bin/command command
```

Now you can run:

```bash
php command migrate
```

---

## 🛠 Usage

### Create a New Migration
```bash
php command make:migration create_users_table
```
This will generate a migration file inside `Application/Database/Migrations`.

### Run Migrations
```bash
php command migrate
```
Applies all pending migrations.

### Fresh Migrations (Reset & Run All)
```bash
php command migrate:fresh
```
Drops all tables and runs migrations from scratch.

### Apply Pending Migrations
```bash
php command migrate:up
```
Runs the next batch of pending migrations.

### Rollback the Last Migration Batch
```bash
php command migrate:down
```
Rolls back the last executed migration batch.

### Refresh Migrations (Rollback & Reapply)
```bash
php command migrate:refresh
```
Rolls back all migrations and runs them again.

---

## 📂 Project Structure
```
my-migration-package/
│── src/
│   ├── Database/
│   │   ├── Core/               # Core migration classes
│   │   │   ├── Migration.php
│   │   │   ├── Schema.php
│   │   │   ├── Blueprint.php
│   │   │   ├── DB.php
│   │   ├── Migrations/         # Auto-generated migrations
│   ├── Bootstrap.php           # Main bootstrap file
│── bin/
│   ├── command                 # CLI command file
│── composer.json
│── README.md
```

---

## 🔧 Configuration

Configure the database connection in `bin/command`:

```php
Regur\LMVC\Framework\Database\Bootstrap::init([
    'host' => '127.0.0.1',
    'database' => 'your_db',
    'username' => 'root',
    'password' => '',
]);
```

---

## 📜 License
This package is open-source and available under the MIT License.

💡 **Need help?** Feel free to create an issue or contribute!
