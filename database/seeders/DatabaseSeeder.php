<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\DocumentInterne;
use App\Models\Personne;
use App\Models\Service;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $annee = now()->year;

        // Super admin
        $admin = User::firstOrCreate(
            ['email' => config('services.auth.super_admin', 'maxime.ponsart@groupe-speed.cloud')],
            ['name' => 'Maxime Ponsart', 'role' => 'admin', 'password' => bcrypt(Str::random(32))]
        );
        $admin->update(['role' => 'admin']);

        // Managers
        $managerMkt = User::firstOrCreate(['email' => 'manager.mktg@groupe-speed.cloud'],
            ['name' => 'Alice Marketing', 'role' => 'manager', 'password' => bcrypt(Str::random(32))]);
        $managerDev = User::firstOrCreate(['email' => 'manager.dev@groupe-speed.cloud'],
            ['name' => 'Bob Dev', 'role' => 'manager', 'password' => bcrypt(Str::random(32))]);
        $userStd = User::firstOrCreate(['email' => 'user.std@groupe-speed.cloud'],
            ['name' => 'Chloé User', 'role' => 'user', 'password' => bcrypt(Str::random(32))]);

        // Services
        $mktg = Service::firstOrCreate(['code' => 'MKTG'],
            ['name' => 'Marketing', 'manager_id' => $managerMkt->id, 'budget_annuel_courant' => 50000, 'budget_restant' => 50000]);
        $dev = Service::firstOrCreate(['code' => 'DEV'],
            ['name' => 'Développement', 'manager_id' => $managerDev->id, 'budget_annuel_courant' => 120000, 'budget_restant' => 120000]);
        $rh = Service::firstOrCreate(['code' => 'RH'],
            ['name' => 'Ressources Humaines', 'manager_id' => null, 'budget_annuel_courant' => 30000, 'budget_restant' => 30000]);

        // Budgets historisés (année courante + N+1)
        foreach ([$mktg, $dev, $rh] as $service) {
            Budget::firstOrCreate(['service_id' => $service->id, 'annee' => $annee],
                ['montant_initial' => $service->budget_annuel_courant, 'montant_depense' => 0]);
        }

        // Personnes
        Personne::firstOrCreate(['user_id' => $managerMkt->id],
            ['service_id' => $mktg->id, 'nom' => 'Alice Marketing', 'tarif_horaire_par_defaut' => 65]);
        Personne::firstOrCreate(['user_id' => $managerDev->id],
            ['service_id' => $dev->id, 'nom' => 'Bob Dev', 'tarif_horaire_par_defaut' => 80]);
        Personne::firstOrCreate(['user_id' => $userStd->id],
            ['service_id' => $dev->id, 'nom' => 'Chloé User', 'tarif_horaire_par_defaut' => 55]);

        // Document exemple
        if (! DocumentInterne::where('demandeur_id', $userStd->id)->exists()) {
            $doc = DocumentInterne::create([
                'numero_document' => DocumentInterne::genererNumero(),
                'service_emetteur_id' => $dev->id,
                'service_destinataire_id' => $mktg->id,
                'date_emission' => now(),
                'description_globale' => 'Développement landing page campagne Q3',
                'taux_tva' => 20,
                'statut' => DocumentInterne::STATUT_BROUILLON,
                'demandeur_id' => $userStd->id,
            ]);
            $personne = Personne::where('user_id', $userStd->id)->first();
            $doc->lignes()->create([
                'description_ligne' => 'Intégration front',
                'type_prestation' => 'Temps Interne',
                'personne_id' => $personne->id,
                'quantite' => 20,
                'tarif_unitaire' => 55,
            ]);
            $doc->lignes()->create([
                'description_ligne' => 'Licence template',
                'type_prestation' => 'Licence / Abonnement',
                'description_achat' => 'Template premium (1 an)',
                'quantite' => 1,
                'tarif_unitaire' => 300,
            ]);
            $doc->lignes()->create([
                'description_ligne' => 'Nom de domaine + hébergement',
                'type_prestation' => 'Achat Externe',
                'description_achat' => 'Prestataire O2Switch',
                'quantite' => 1,
                'tarif_unitaire' => 120,
            ]);
            $doc->lignes()->create([
                'description_ligne' => 'Shooting photo produit',
                'type_prestation' => 'Prestation externe',
                'description_achat' => 'Studio partenaire',
                'quantite' => 1,
                'tarif_unitaire' => 450,
            ]);
            $doc->recalculerTotal();
        }
    }
}
