<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute le suivi des crédits perçus par un service émetteur lorsqu'un
     * document qu'il a facturé est validé — pendant symétrique de
     * montant_depense, pour un fonctionnement « compte en banque » entre
     * services (le destinataire paie, l'émetteur est crédité).
     */
    public function up(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->decimal('montant_credite', 14, 2)->default(0)->after('montant_depense');
        });
    }

    public function down(): void
    {
        Schema::table('budgets', function (Blueprint $table) {
            $table->dropColumn('montant_credite');
        });
    }
};
