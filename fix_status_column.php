<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

try {
    // Vérifier la taille actuelle de la colonne status
    $result = \DB::select("SHOW COLUMNS FROM document_requests LIKE 'status'");
    
    if (!empty($result)) {
        $column = $result[0];
        echo "État actuel de la colonne status:\n";
        echo "Type: " . $column->Type . "\n";
        echo "Null: " . $column->Null . "\n";
        echo "Key: " . $column->Key . "\n\n";
        
        // Modifier la colonne pour augmenter la taille
        \DB::statement("ALTER TABLE document_requests MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'en cours'");
        
        echo "✅ Colonne 'status' modifiée avec succès (VARCHAR(20))\n";
        
        // Vérifier après modification
        $result2 = \DB::select("SHOW COLUMNS FROM document_requests LIKE 'status'");
        if (!empty($result2)) {
            echo "Nouvel état: " . $result2[0]->Type . "\n";
        }
    } else {
        echo "❌ Colonne 'status' non trouvée\n";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\nOpération terminée.\n";
