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
            if (!Schema::hasColumn('properties', 'amenities')) {
                $table->text('amenities')->nullable()->after('feature');
            }
            if (!Schema::hasColumn('properties', 'property_type')) {
                $table->string('property_type')->nullable()->after('type');
            }
            if (!Schema::hasColumn('properties', 'housing_context')) {
                $table->string('housing_context')->nullable()->after('property_type');
            }
            if (!Schema::hasColumn('properties', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('city');
            }
            if (!Schema::hasColumn('properties', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            if (!Schema::hasColumn('properties', 'availability_status')) {
                $table->string('availability_status')->default('active')->after('status');
            }
            if (!Schema::hasColumn('properties', 'likes')) {
                $table->integer('likes')->default(0)->after('like');
            }
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
