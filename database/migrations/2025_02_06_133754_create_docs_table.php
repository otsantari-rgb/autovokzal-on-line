<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('docs', function (Blueprint $table) {
            $table->id();
            $table->string('firstname');
            $table->string('lastname');
            $table->string('patronymic')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->enum('type', ['passport', 'birth_certificate']);
            $table->string('number');
            $table->date('birthday');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['firstname', 'lastname', 'patronymic', 'gender', 'type', 'number', 'birthday', 'user_id'], 'unique_docs');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('docs');
    }
};
