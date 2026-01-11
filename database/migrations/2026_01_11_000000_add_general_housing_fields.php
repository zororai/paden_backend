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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('housing_context', ['general', 'student'])->default('student')->after('role');
            $table->boolean('profile_complete')->default(false)->after('housing_context');
            $table->string('preferred_contact')->nullable()->after('phone');
            $table->boolean('whatsapp_enabled')->default(false)->after('preferred_contact');
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->enum('property_type', ['room', 'cottage', 'flat', 'house'])->nullable()->after('type');
            $table->enum('housing_context', ['general', 'student'])->default('student')->after('property_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['housing_context', 'profile_complete', 'preferred_contact', 'whatsapp_enabled']);
        });

        Schema::table('properties', function (Blueprint $table) {
            $table->dropColumn(['property_type', 'housing_context']);
        });
    }
};
