<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Create tables manually in MySQL
Schema::create('users', function ($table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->enum('role', ['citizen', 'admin'])->default('citizen');
    $table->string('phone')->nullable();
    $table->string('cni_number')->nullable();
    $table->date('birth_date')->nullable();
    $table->string('birth_place')->nullable();
    $table->text('address')->nullable();
    $table->string('profession')->nullable();
    $table->string('nationality')->default('Guinéenne');
    $table->rememberToken();
    $table->timestamps();
});

echo "Users table created\n";

Schema::create('document_requests', function ($table) {
    $table->id();
    $table->string('reference')->unique();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->enum('document_type', ['cni', 'passeport', 'permis']);
    $table->string('first_name');
    $table->string('last_name');
    $table->date('birth_date');
    $table->string('birth_place');
    $table->text('address');
    $table->string('phone');
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

Schema::create('documents', function ($table) {
    $table->id();
    $table->string('reference')->unique();
    $table->foreignId('user_id')->constrained()->onDelete('cascade');
    $table->foreignId('request_id')->constrained('document_requests')->onDelete('cascade');
    $table->enum('document_type', ['cni', 'passeport', 'permis']);
    $table->string('holder_name');
    $table->date('birth_date');
    $table->string('birth_place');
    $table->date('issue_date');
    $table->date('expiry_date');
    $table->string('qr_code')->unique();
    $table->boolean('is_valid')->default(true);
    $table->timestamp('revoked_at')->nullable();
    $table->text('revocation_reason')->nullable();
    $table->timestamps();
});

echo "Documents table created\n";

echo "All MySQL tables created successfully!\n";
