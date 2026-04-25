<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';

try {
    $kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
    echo "Kernel class: " . get_class($kernel) . "\n";
    
    // Try to handle serve command
    $status = $kernel->handle(
        $input = new \Symfony\Component\Console\Input\ArgvInput,
        $output = new \Symfony\Component\Console\Output\ConsoleOutput
    );
    
    echo "Status: " . $status . "\n";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
