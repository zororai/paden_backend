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
        Schema::table('properties', function (Blueprint $table) {
            $table->text('amenities')->nullable()->after('feature');
            $table->string('property_type')->nullable()->after('type');
            $table->string('housing_context')->nullable()->after('property_type');
            $table->decimal('latitude', 10, 8)->nullable()->after('city');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('availability_status')->default('active')->after('status');
            $table->integer('likes')->default(0)->after('like');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['amenities', 'property_type', 'housing_context', 'latitude', 'longitude', 'availability_status', 'likes']);
        });
    }
};
