<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    // Mettre à jour la colonne document_type dans les deux tables
    Schema::table('document_requests', function ($table) {
        $table->string('document_type', 20)->change();
    });

    Schema::table('documents', function ($table) {
        $table->string('document_type', 20)->change();
    });

    echo "Colonnes document_type mises à jour avec succès\n";

} catch (Exception $e) {
    echo "Erreur: " . $e->getMessage() . "\n";
}
