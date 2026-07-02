<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Passe `type_prestation` d'un enum figé à une chaîne, afin d'autoriser
     * de nouveaux types de prestation / produit sans contrainte CHECK.
     */
    public function up(): void
    {
        Schema::table('lignes_documents', function (Blueprint $table) {
            $table->string('type_prestation', 60)->change();
        });
    }

    public function down(): void
    {
        Schema::table('lignes_documents', function (Blueprint $table) {
            $table->enum('type_prestation', ['Temps Interne', 'Achat Externe'])->change();
        });
    }
};
