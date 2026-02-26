<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->unsignedSmallInteger('carried_cube_index')->nullable()->after('rotation');
            $table->json('cube_position')->nullable()->after('carried_cube_index');
            $table->json('cube_rotation')->nullable()->after('cube_position');
            $table->unsignedSmallInteger('focused_cube_index')->nullable()->after('cube_rotation');
        });
    }

    public function down(): void
    {
        Schema::table('game_sessions', function (Blueprint $table) {
            $table->dropColumn(['carried_cube_index', 'cube_position', 'cube_rotation', 'focused_cube_index']);
        });
    }
};
