<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('mailings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->morphs('mailable');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mailings');
    }
};
