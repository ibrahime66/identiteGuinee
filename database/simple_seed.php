<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use App\Models\User;
use App\Models\DocumentRequest;
use App\Models\Document;
use Illuminate\Support\Facades\Hash;

// Create admin user
$admin = User::create([
    'name' => 'Administrateur Principal',
    'email' => 'admin@identiguinee.gn',
    'password' => Hash::make('admin123'),
    'role' => 'admin',
    'phone' => '+224 622 12 34 56',
    'nationality' => 'Guinéenne',
]);

echo "Admin user created\n";

// Create demo citizen
$citizen = User::create([
    'name' => 'Mamadou Diallo',
    'email' => 'citoyen@identiguinee.gn',
    'password' => Hash::make('password'),
    'role' => 'citizen',
    'phone' => '+224 622 12 34 57',
    'cni_number' => 'CNI-2020-000001',
    'birth_date' => '1990-05-15',
    'birth_place' => 'Conakry',
    'address' => 'Rue du Commerce, Dixinn, Conakry',
    'profession' => 'Comptable',
    'nationality' => 'Guinéenne',
]);

echo "Citizen user created\n";

echo "Database seeded successfully!\n";
echo "Admin: admin@identiguinee.gn / admin123\n";
echo "Citizen: citoyen@identiguinee.gn / password\n";
