<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

try {
    echo "Creating remaining tables...\n";

    // Create password_reset_tokens table
    if (!Schema::hasTable('password_reset_tokens')) {
        Schema::create('password_reset_tokens', function ($table) {
            $table->string('email', 191)->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
        echo "password_reset_tokens table created\n";
    } else {
        echo "password_reset_tokens table already exists\n";
    }

    // Create sessions table
    if (!Schema::hasTable('sessions')) {
        Schema::create('sessions', function ($table) {
            $table->string('id', 191)->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
        echo "sessions table created\n";
    } else {
        echo "sessions table already exists\n";
    }

    // Create jobs table
    if (!Schema::hasTable('jobs')) {
        Schema::create('jobs', function ($table) {
            $table->id();
            $table->string('queue', 191)->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });
        echo "jobs table created\n";
    } else {
        echo "jobs table already exists\n";
    }

    // Create failed_jobs table
    if (!Schema::hasTable('failed_jobs')) {
        Schema::create('failed_jobs', function ($table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });
        echo "failed_jobs table created\n";
    } else {
        echo "failed_jobs table already exists\n";
    }

    echo "\nManual migration completed successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
}
