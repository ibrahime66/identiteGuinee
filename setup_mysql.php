<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\DocumentRequest;
use App\Models\Document;
use Illuminate\Support\Facades\Hash;

try {
    // Drop tables if they exist
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    Schema::dropIfExists('documents');
    Schema::dropIfExists('document_requests');
    Schema::dropIfExists('users');
    Schema::dropIfExists('migrations');
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    // Create migrations table first
    Schema::create('migrations', function ($table) {
        $table->id();
        $table->string('migration', 255);
        $table->integer('batch');
    });

    // Create users table with proper field lengths
    Schema::create('users', function ($table) {
        $table->id();
        $table->string('name', 191);
        $table->string('email', 191)->unique();
        $table->timestamp('email_verified_at')->nullable();
        $table->string('password', 255);
        $table->enum('role', ['citizen', 'admin'])->default('citizen');
        $table->string('phone', 20)->nullable();
        $table->string('cni_number', 50)->nullable();
        $table->date('birth_date')->nullable();
        $table->string('birth_place', 100)->nullable();
        $table->text('address')->nullable();
        $table->string('profession', 100)->nullable();
        $table->string('nationality', 50)->default('Guinéenne');
        $table->rememberToken();
        $table->timestamps();
    });

    echo "Users table created\n";

    // Create document_requests table
    Schema::create('document_requests', function ($table) {
        $table->id();
        $table->string('reference', 50)->unique();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->enum('document_type', ['cni', 'passeport', 'permis']);
        $table->string('first_name', 191);
        $table->string('last_name', 191);
        $table->date('birth_date');
        $table->string('birth_place', 100);
        $table->text('address');
        $table->string('phone', 20);
        $table->enum('status', ['en cours', 'validée', 'rejetée'])->default('en cours');
        $table->enum('priority', ['normal', 'urgent'])->default('normal');
        $table->text('notes')->nullable();
        $table->text('rejection_reason')->nullable();
        $table->timestamp('validated_at')->nullable();
        $table->timestamp('rejected_at')->nullable();
        $table->foreignId('validated_by')->nullable()->constrained('users')->onDelete('set null');
        $table->foreignId('rejected_by')->nullable()->constrained('users')->onDelete('set null');
        $table->timestamps();
    });

    echo "Document requests table created\n";

    // Create documents table
    Schema::create('documents', function ($table) {
        $table->id();
        $table->string('reference', 50)->unique();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('request_id')->constrained('document_requests')->onDelete('cascade');
        $table->enum('document_type', ['cni', 'passeport', 'permis']);
        $table->string('holder_name', 191);
        $table->date('birth_date');
        $table->string('birth_place', 100);
        $table->date('issue_date');
        $table->date('expiry_date');
        $table->string('qr_code', 50)->unique();
        $table->boolean('is_valid')->default(true);
        $table->timestamp('revoked_at')->nullable();
        $table->text('revocation_reason')->nullable();
        $table->timestamps();
    });

    echo "Documents table created\n";

    // Insert initial data

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

    echo "MySQL database setup completed successfully!\n";
    echo "Admin: admin@identiguinee.gn / admin123\n";
    echo "Citizen: citoyen@identiguinee.gn / password\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
