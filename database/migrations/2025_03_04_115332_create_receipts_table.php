<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->uuid()->unique(); // UUID чека
            $table->string('external_id')->unique(); // ID, который мы передаем в запросе
            $table->string('status'); // Статус чека (например, 'done', 'fail')
            $table->decimal('total', 10, 2)->nullable(); // Итоговая сумма
            $table->integer('fiscal_receipt_number')->nullable(); // Номер фискального чека
            $table->integer('shift_number')->nullable(); // Номер смены
            $table->dateTime('receipt_datetime')->nullable(); // Дата и время чека
            $table->string('fn_number')->nullable(); // ФН (фискальный накопитель)
            $table->string('ecr_registration_number')->nullable(); // Регистрационный номер ККТ
            $table->integer('fiscal_document_number')->nullable(); // Фискальный номер документа
            $table->bigInteger('fiscal_document_attribute')->nullable(); // Атрибут ФД
            $table->string('fns_site')->nullable(); // Сайт ФНС
            $table->string('ofd_inn')->nullable(); // ИНН ОФД
            $table->string('ofd_receipt_url')->nullable(); // Ссылка на чек в ОФД
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
