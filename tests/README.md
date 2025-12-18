# Tests

This project uses **Pest** for automated testing. Tests are organized into groups, allowing you to run specific sets of tests.

## Install Dependencies

```bash
composer install
```

## Run Tests

### Run All Tests

```bash
php ./vendor/bin/pest
```

### Run Specific Test Groups

```bash
# Make Migration Tests
php ./vendor/bin/pest --group=makemigration

# Make Seeder Tests
php ./vendor/bin/pest --group=makeseeder

# Migration Tests
php ./vendor/bin/pest --group=migrate

# Seed Tests
php ./vendor/bin/pest --group=seed
```

## Notes (for developers)

- Test groups are defined using Pest’s `->group()` method.
- Multiple groups can be run together, but it is not convenient way because every sets has individual task and also depends on manual interaction with migration files so please run test with specific groups:

```bash
php ./vendor/bin/pest --group=makemigration,makeseeder
```
