<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recurring_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('category', ['personnel', 'hebergement', 'infrastructure', 'marketing', 'locaux', 'autre']);
            $table->decimal('amount', 10, 2);
            $table->date('start_month');
            $table->date('end_month')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recurring_expenses');
    }
};
