<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Foreign key constraints in documents table:\n";
$fks = DB::select("PRAGMA foreign_key_list('documents')");

foreach ($fks as $fk) {
    echo "- " . $fk->from . " -> " . $fk->table . "." . $fk->to . "\n";
}
