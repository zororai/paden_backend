<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop foreign key and unique constraint using raw SQL for MySQL compatibility
        DB::statement('ALTER TABLE room_share_requests DROP FOREIGN KEY room_share_requests_receiver_id_foreign');
        DB::statement('ALTER TABLE room_share_requests DROP INDEX room_share_requests_sender_id_receiver_id_property_id_unique');

        Schema::table('room_share_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('receiver_id')->nullable()->change();
            $table->string('university')->after('property_id');
        });

        Schema::table('room_share_requests', function (Blueprint $table) {
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['sender_id', 'property_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_share_requests', function (Blueprint $table) {
            $table->dropForeign(['receiver_id']);
            $table->dropUnique(['sender_id', 'property_id']);
            $table->dropColumn('university');
        });

        Schema::table('room_share_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('receiver_id')->nullable(false)->change();
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['sender_id', 'receiver_id', 'property_id']);
        });
    }
};
