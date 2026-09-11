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
        \App\Models\Lookup::firstOrCreate([
            'name' => 'max_registered_users',
        ], [
            'value' => '1000',
            'record_state' => 1,
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\Lookup::where('name', 'max_registered_users')->delete();
    }
};
