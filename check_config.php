<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$migrationsConfig = config('database.migrations');

echo "Migrations config:\n";
var_dump($migrationsConfig);

echo "\nMigrations table:\n";
var_dump(config('database.migrations.table'));

echo "\nMigrations table type:\n";
var_dump(gettype(config('database.migrations.table')));

echo "\nDatabase prefix:\n";
var_dump(config('database.connections.mysql.prefix'));
