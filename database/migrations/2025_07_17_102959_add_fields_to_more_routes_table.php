<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('more_routes', function (Blueprint $table) {
            $table->string('start_translated')->nullable();
            $table->string('end_translated')->nullable();
            $table->string('name')->nullable();
            $table->integer('price')->nullable();
            $table->string('time')->nullable();
            $table->string('lunch')->nullable();
            $table->string('phone')->nullable();
            $table->string('coordinates_from_lat')->nullable();
            $table->string('coordinates_from_long')->nullable();
            $table->string('coordinates_to_lat')->nullable();
            $table->string('coordinates_to_long')->nullable();
            $table->string('route_name')->nullable();
            
        });
    }

    public function down(): void
    {
        Schema::table('more_routes', function (Blueprint $table) {
            $table->dropColumn('start_translated');
            $table->dropColumn('end_translated')();
            $table->dropColumn('name')();
            $table->dropColumn('price')();
            $table->dropColumn('time')();
            $table->dropColumn('lunch')();
            $table->dropColumn('phone')();
            $table->dropColumn('coordinates_from_lat')();
            $table->dropColumn('coordinates_from_long')();
            $table->dropColumn('coordinates_to_lat')();
            $table->dropColumn('coordinates_to_long')();
            $table->dropColumn('route_name')();
        });
    }
};

