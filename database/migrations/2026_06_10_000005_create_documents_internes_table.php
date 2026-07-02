<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('documents_internes', function (Blueprint $table) {
            $table->id();
            $table->string('numero_document')->unique();
            $table->foreignId('service_emetteur_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('service_destinataire_id')->constrained('services')->cascadeOnDelete();
            $table->date('date_emission');
            $table->text('description_globale')->nullable();
            $table->decimal('montant_total_ht', 14, 2)->default(0);
            $table->enum('statut', ['Brouillon', 'En attente de validation', 'Validé', 'Refusé', 'Archivé'])->default('Brouillon');
            $table->foreignId('demandeur_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('validateur_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('date_validation')->nullable();
            $table->text('motif_refus')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documents_internes');
    }
};
