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
        Schema::create('properties', function (Blueprint $table) {
            $table->id('id');
            $table->string('title');
            $table->string('pcontent');
            $table->string('type');
            $table->string('bhk');
            $table->string('stype');
            $table->string('bedroom');
            $table->string('bathroom');
            $table->string('balcony');
            $table->string('kitchen');
            $table->string('hall');
            $table->string('floor');
            $table->string('size');
            $table->string('price');
            $table->string('location');
            $table->string('city');
            $table->string('state');
            $table->string('feature');
            $table->string('pimage');
            $table->string('pimage1');
            $table->string('pimage2');
            $table->string('pimage3');
            $table->string('pimage4');
            $table->string('uid');
            $table->string('status');
            $table->string('mapimage');
            $table->string('topmapimage');
            $table->string('groundmapimage');
            $table->string('totalfloor');
            $table->date('date');
            $table->string('isFeatured');

            $table->integer('count')->default(0);
            $table->integer('like')->default(0);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
