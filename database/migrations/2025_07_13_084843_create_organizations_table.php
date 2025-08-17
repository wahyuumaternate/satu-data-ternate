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
            $table->string('email')->nullable(); // Email kontak
            $table->string('phone')->nullable(); // Telepon
            $table->text('address')->nullable(); // Alamat
            $table->string('website')->nullable(); // Website
            $table->timestamps();

            // Indexes
            $table->index('name');
            $table->index('type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('organizations');
    }
};