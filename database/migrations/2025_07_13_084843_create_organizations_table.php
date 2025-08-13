<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama organisasi/OPD
            $table->string('logo'); // Nama organisasi/OPD
            $table->string('code')->unique()->nullable(); // Kode organisasi
            $table->text('description')->nullable(); // Deskripsi
            $table->string('type')->nullable(); // Jenis: OPD, Dinas, Badan, dll
            $table->string('email')->nullable(); // Email kontak
            $table->string('phone')->nullable(); // Telepon
            $table->text('address')->nullable(); // Alamat
            $table->string('website')->nullable(); // Website
            $table->foreignId('parent_id')->nullable()->constrained('organizations')->onDelete('set null'); // Untuk hierarki organisasi
            $table->boolean('is_active')->default(true); // Status aktif
            $table->json('metadata')->nullable(); // Data tambahan dalam JSON
            $table->timestamps();

            // Indexes
            $table->index('name');
            $table->index('type');
            $table->index('is_active');
            $table->index('parent_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('organizations');
    }
};