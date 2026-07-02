<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lignes_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_interne_id')->constrained('documents_internes')->cascadeOnDelete();
            $table->string('description_ligne');
            $table->enum('type_prestation', ['Temps Interne', 'Achat Externe']);
            $table->foreignId('personne_id')->nullable()->constrained('personnes')->nullOnDelete();
            $table->string('description_achat')->nullable();
            $table->decimal('quantite', 12, 2)->default(0);
            $table->decimal('tarif_unitaire', 12, 2)->default(0);
            $table->decimal('montant_ligne', 14, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lignes_documents');
    }
};
