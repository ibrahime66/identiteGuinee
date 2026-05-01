<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

// Drop and recreate documents table with correct foreign key
Schema::dropIfExists('documents');

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

echo "Documents table recreated with correct foreign key constraint\n";
