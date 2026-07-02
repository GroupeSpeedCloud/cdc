<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('budgets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->integer('annee');
            $table->decimal('montant_initial', 14, 2)->default(0);
            $table->decimal('montant_depense', 14, 2)->default(0);
            $table->timestamps();
            $table->unique(['service_id', 'annee']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('budgets');
    }
};
