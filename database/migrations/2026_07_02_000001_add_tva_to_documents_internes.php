<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('documents_internes', function (Blueprint $table) {
            $table->decimal('taux_tva', 5, 2)->default(20)->after('montant_total_ht');
            $table->decimal('montant_tva', 14, 2)->default(0)->after('taux_tva');
            $table->decimal('montant_total_ttc', 14, 2)->default(0)->after('montant_tva');
            $table->date('date_echeance')->nullable()->after('date_emission');
            $table->text('notes')->nullable()->after('description_globale');
        });
    }

    public function down(): void
    {
        Schema::table('documents_internes', function (Blueprint $table) {
            $table->dropColumn(['taux_tva', 'montant_tva', 'montant_total_ttc', 'date_echeance', 'notes']);
        });
    }
};
