<?php

echo "PHP Server Test - Working!";
echo "<br>";
echo "PHP Version: " . phpversion();
echo "<br>";
echo "Current directory: " . __DIR__;
echo "<br>";
echo "Document root: " . $_SERVER['DOCUMENT_ROOT'] ?? 'Not set';

// Test Laravel loading
try {
    require __DIR__.'/../vendor/autoload.php';
    echo "<br>Autoload: SUCCESS";
    
    $app = require __DIR__.'/../bootstrap/app.php';
    echo "<br>Bootstrap: SUCCESS";
    
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
    echo "<br>Kernel: SUCCESS";
    
    echo "<br><br><strong>Laravel can load successfully!</strong>";
    
} catch (Exception $e) {
    echo "<br>Error: " . $e->getMessage();
}
