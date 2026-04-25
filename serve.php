<?php

echo "=== SERVEUR LARAVEl POUR WINDOWS ===\n";
echo "Démarrage du serveur sur http://127.0.0.1:8000\n";
echo "Appuyez sur Ctrl+C pour arrêter\n\n";

// Lancer le serveur PHP intégré directement
$host = '127.0.0.1';
$port = 8000;
$docroot = __DIR__ . '/public';

// Commande Windows pour lancer le serveur
$command = "php -S {$host}:{$port} -t {$docroot}";

echo "Serveur lancé sur: http://{$host}:{$port}\n";
echo "Document root: {$docroot}\n";
echo "Tapez Ctrl+C pour arrêter\n\n";

// Exécuter la commande
system($command);
