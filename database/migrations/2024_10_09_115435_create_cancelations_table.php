<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cancelations', function (Blueprint $table) {
            $table->id();
            $table->string('causer');
            $table->dateTime('date');
            $table->longText('reason')->nullable();
            $table->morphs('model');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cancelations');
    }
};
