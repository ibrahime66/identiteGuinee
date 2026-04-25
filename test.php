<?php

define('LARAVEL_START', microtime(true));

echo "1. Testing autoload...\n";
require __DIR__.'/vendor/autoload.php';
echo "2. Autoload loaded successfully\n";

echo "3. Loading bootstrap...\n";
$app = require_once __DIR__.'/bootstrap/app.php';
echo "4. Bootstrap loaded successfully\n";

echo "5. Making kernel...\n";
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
echo "6. Kernel created successfully\n";

echo "7. Testing complete!\n";
