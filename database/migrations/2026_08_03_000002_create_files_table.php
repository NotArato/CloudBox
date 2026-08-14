<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('folder_id')->nullable()->constrained('folders')->onDelete('cascade');
            $table->string('name'); // Display title / filename
            $table->string('original_name');
            $table->string('storage_path');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0); // in bytes
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('files');
    }
};
