<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('value');
        });

        DB::table('settings')->insert([
            ['key' => 'homepage_variant', 'value' => 'original'],
            ['key' => 'visualization_mode', 'value' => 'animated'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
