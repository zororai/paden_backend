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
        Schema::table('room_share_requests', function (Blueprint $table) {
            $table->string('preferred_year')->nullable()->after('message');
            $table->string('preferred_gender')->nullable()->after('preferred_year');
            $table->text('rent_sharing_conditions')->nullable()->after('preferred_gender');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_share_requests', function (Blueprint $table) {
            $table->dropColumn(['preferred_year', 'preferred_gender', 'rent_sharing_conditions']);
        });
    }
};
