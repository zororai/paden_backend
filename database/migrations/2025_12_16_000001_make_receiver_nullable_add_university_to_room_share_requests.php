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
        $senderFkExists = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'room_share_requests' 
            AND CONSTRAINT_NAME = 'room_share_requests_sender_id_foreign'
        ");
        
        if (!empty($senderFkExists)) {
            DB::statement('ALTER TABLE room_share_requests DROP FOREIGN KEY room_share_requests_sender_id_foreign');
        }
        
        $propertyFkExists = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'room_share_requests' 
            AND CONSTRAINT_NAME = 'room_share_requests_property_id_foreign'
        ");
        
        if (!empty($propertyFkExists)) {
            DB::statement('ALTER TABLE room_share_requests DROP FOREIGN KEY room_share_requests_property_id_foreign');
        }
        
        $uniqueExists = DB::select("
            SELECT INDEX_NAME 
            FROM information_schema.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'room_share_requests' 
            AND INDEX_NAME = 'room_share_requests_sender_id_receiver_id_property_id_unique'
        ");
        
        if (!empty($uniqueExists)) {
            DB::statement('ALTER TABLE room_share_requests DROP INDEX room_share_requests_sender_id_receiver_id_property_id_unique');
        }

        $universityExists = DB::select("
            SELECT COLUMN_NAME 
            FROM information_schema.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'room_share_requests' 
            AND COLUMN_NAME = 'university'
        ");

        Schema::table('room_share_requests', function (Blueprint $table) use ($universityExists) {
            $table->unsignedBigInteger('receiver_id')->nullable()->change();
            if (empty($universityExists)) {
                $table->string('university')->after('property_id');
            }
        });

        Schema::table('room_share_requests', function (Blueprint $table) {
            $table->foreign('sender_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
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
