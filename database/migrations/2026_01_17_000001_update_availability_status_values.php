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
        // Update existing 'active' values to 'available'
        DB::table('properties')
            ->where('availability_status', 'active')
            ->update(['availability_status' => 'available']);
        
        // Update existing 'inactive' values to 'occupied'
        DB::table('properties')
            ->where('availability_status', 'inactive')
            ->update(['availability_status' => 'occupied']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to old values
        DB::table('properties')
            ->where('availability_status', 'available')
            ->update(['availability_status' => 'active']);
        
        DB::table('properties')
            ->where('availability_status', 'occupied')
            ->update(['availability_status' => 'inactive']);
    }
};
