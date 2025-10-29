# Système de Gestion des Vacataires - Documentation

## Vue d'ensemble

Ce système permet la gestion complète des vacataires (employés temporaires payés à l'heure) dans l'application DigitalHR. Les vacataires utilisent la même application mobile que les employés permanents pour scanner leur présence, mais leur salaire est calculé différemment (heures × taux horaire).

## Fonctionnalités Implémentées

### 1. Gestion des Employés Vacataires

#### Types d'employés
- **Permanent** : Salaire mensuel fixe avec système de pénalités
- **Semi-Permanent** : Traité comme permanent
- **Vacataire** : Salaire horaire calculé automatiquement

#### Champs spécifiques aux vacataires
- `hourly_rate` : Taux horaire en FCFA
- `contract_start_date` : Date de début du contrat
- `contract_end_date` : Date de fin (optionnel pour contrat indéterminé)
- `specialization` : Spécialité/matière enseignée
- `max_hours_per_month` : Quota maximum d'heures par mois (optionnel)
- `contract_status` : Statut du contrat (active, expired, terminated)

### 2. Gestion des Contrats

#### Table `vacataire_contracts`
Maintient l'historique complet de tous les contrats :
- Numéro de contrat unique
- Dates de début et fin
- Taux horaire au moment du contrat
- Type de contrat (initial, renewal, amendment)
- Statut (active, expired, renewed, terminated)

#### Fonctionnalités
- **Création automatique** : Un contrat est créé automatiquement lors de la création d'un vacataire
- **Renouvellement** : Possibilité de renouveler un contrat avec nouveau taux/quota
- **Terminaison** : Terminer un contrat avec raison documentée
- **Vérification automatique** : Détection des contrats expirés

### 3. Calcul Automatique des Salaires

#### Lors du scan de présence (check-out)
```
Heures travaillées = (total_working_duration - total_lunch_duration) / 60
Salaire journalier = Heures travaillées × Taux horaire
```

#### Champs ajoutés à `attendances`
- `hourly_rate` : Taux horaire au moment de la présence
- `daily_salary` : Salaire calculé pour la journée
- `is_validated` : Présence validée par l'admin
- `validated_by` : Admin qui a validé
- `validated_at` : Date de validation

### 4. Gestion des Paiements Mensuels

#### Table `vacataire_monthly_payments`
Enregistre les paiements mensuels avec :
- Période (mois/année)
- Total heures et jours travaillés
- Salaire brut
- Déductions et bonus
- Salaire net
- Statut du paiement (pending → validated → paid)

#### Workflow de paiement
1. **Génération** : Admin génère les paies pour un mois donné
2. **Validation** : Admin vérifie et valide les paies
3. **Ajustements** : Ajout de déductions/bonus si nécessaire
4. **Paiement** : Marquer comme payé avec méthode et référence
5. **Fiche de paie** : Génération PDF pour remise au vacataire

### 5. Alertes et Notifications

Le système génère des alertes pour :
- **Contrats expirant** : Alerte 30 jours avant expiration
- **Quota dépassé** : Alerte quand un vacataire atteint 90% de son quota
- **Validation en attente** : Nombre de paies en attente de validation

## Structure des Fichiers

### Migrations
```
database/migrations/
├── 2025_10_27_235541_add_vacataire_fields_to_users_table.php
├── 2025_10_27_235702_create_vacataire_contracts_table.php
├── 2025_10_27_235703_create_vacataire_monthly_payments_table.php
└── 2025_10_27_235704_add_vacataire_fields_to_attendances_table.php
```

### Models
```
app/Models/
├── VacataireContract.php (nouveau)
├── VacataireMonthlyPayment.php (nouveau)
├── User.php (modifié - ajout relations et méthodes)
└── Attendance.php (modifié - ajout champs vacataires)
```

### Services
```
app/Http/Services/
├── VacataireServices.php (nouveau)
├── VacatairePaymentServices.php (nouveau)
├── UserServices.php (modifié - gestion création vacataires)
└── AttendanceServices.php (modifié - calcul salaire vacataires)
```

### Controller
```
app/Http/Controllers/Admin/
└── VacataireController.php (nouveau)
```

### Vues
```
resources/views/admin/vacataires/
├── index.blade.php (liste des vacataires)
├── show.blade.php (détails vacataire)
├── payslip_pdf.blade.php (template PDF fiche de paie)
├── reports.blade.php (rapports et statistiques)
└── payments/
    └── index.blade.php (gestion des paiements mensuels)
```

### Routes
```
routes/web.php
Section: admin/vacataires (15 routes ajoutées)
```

## Routes Disponibles

### Gestion des Vacataires
- `GET /admin/vacataires` - Liste des vacataires
- `GET /admin/vacataires/{id}` - Détails d'un vacataire
- `POST /admin/vacataires/{id}/renew` - Renouveler contrat
- `POST /admin/vacataires/{id}/terminate` - Terminer contrat

### Gestion des Paiements
- `GET /admin/vacataires/payments/list` - Liste des paiements
- `POST /admin/vacataires/payments/generate` - Générer paies du mois
- `POST /admin/vacataires/payments/{id}/validate` - Valider une paie
- `POST /admin/vacataires/payments/{id}/mark-paid` - Marquer comme payé
- `POST /admin/vacataires/payments/{id}/cancel` - Annuler une paie
- `POST /admin/vacataires/payments/{id}/adjustments` - Modifier ajustements
- `GET /admin/vacataires/payments/{id}/payslip` - Télécharger fiche PDF
- `GET /admin/vacataires/payments/export` - Exporter en Excel

### Rapports
- `GET /admin/vacataires/reports` - Rapports et statistiques

## Utilisation

### Créer un Vacataire

1. Aller sur "Employee" → "Create Employee"
2. Sélectionner "Type d'Employé" : **Vacataire**
3. Remplir les champs obligatoires :
   - Taux Horaire (FCFA/heure)
   - Date Début Contrat
   - Date Fin Contrat (optionnel)
   - Spécialité (optionnel)
   - Quota Max Heures/Mois (optionnel)

Un contrat est automatiquement créé lors de la sauvegarde.

### Scanner la Présence (Vacataire)

Le vacataire utilise l'application mobile comme un employé permanent :
1. Login avec email/password
2. Scanner son visage pour check-in
3. Scanner son visage pour check-out

**Calcul automatique** : Dès le check-out, le système calcule :
- Heures travaillées (hors pause déjeuner)
- Salaire journalier = heures × taux horaire

### Générer les Paies Mensuelles

1. Aller sur "Vacataires" → "Gestion Paiements"
2. Cliquer "Générer Paies du Mois"
3. Sélectionner mois et année
4. Cliquer "Générer"

Le système :
- Récupère toutes les présences validées
- Calcule le total heures et salaire
- Crée un enregistrement de paiement pour chaque vacataire

### Workflow de Validation

1. **Génération** : Statut = `pending`
2. **Ajustements** : Admin peut ajouter déductions/bonus
3. **Validation** : Admin clique "Valider" → Statut = `validated`
4. **Paiement** : Admin marque comme payé → Statut = `paid`
5. **Fiche de paie** : Télécharger PDF pour remise au vacataire

### Renouveler un Contrat

1. Aller sur détails du vacataire
2. Cliquer "Renouveler Contrat"
3. Saisir nouvelle date de fin
4. Optionnel : Modifier taux horaire ou quota
5. Valider

Le système :
- Marque l'ancien contrat comme "renewed"
- Crée un nouveau contrat avec nouveau numéro
- Met à jour les informations de l'utilisateur

## API Services Disponibles

### VacataireServices

```php
// Obtenir tous les vacataires avec filtres
getAllVacataires($filters)

// Statistiques d'un vacataire pour un mois
getVacataireStats($userId, $month, $year)

// Renouveler contrat
renewContract($userId, $newEndDate, $newHourlyRate = null, $newMaxHours = null)

// Terminer contrat
terminateContract($userId, $reason)

// Vérifier contrats expirés
checkExpiredContracts()

// Obtenir les alertes
getContractAlerts()
```

### VacatairePaymentServices

```php
// Générer paie pour un vacataire
generateMonthlyPayment($userId, $month, $year)

// Générer paies pour tous les vacataires
generateAllMonthlyPayments($month, $year)

// Valider une paie
validatePayment($paymentId, $adminId)

// Marquer comme payé
markAsPaid($paymentId, $paymentMethod, $reference = null)

// Annuler une paie
cancelPayment($paymentId, $reason)

// Ajouter ajustements
updatePaymentAdjustments($paymentId, $deductions, $bonuses, $notes)

// Générer PDF fiche de paie
generatePayslipPDF($paymentId)

// Exporter Excel
exportMonthlyPaymentsExcel($month, $year)

// Statistiques globales
getGlobalStats($month, $year)
```

## Statistiques et Rapports

La page rapports affiche :
- Total vacataires actifs
- Total heures travaillées
- Coût total mensuel
- Moyenne heures/vacataire
- Moyenne salaire
- Répartition par statut (pending/validated/paid)
- Évolution sur 6 mois
- Alertes en temps réel

## Notes Importantes

### Différences avec Employés Permanents

| Aspect | Permanent/Semi-Permanent | Vacataire |
|--------|-------------------------|-----------|
| Salaire | Mensuel fixe | Horaire variable |
| Calcul | Avec pénalités retard | Heures × taux |
| Contrat | CDI généralement | CDD avec dates |
| Quota | Non applicable | Optionnel (max heures/mois) |
| Paiement | Mensuel fixe | Selon heures travaillées |

### Validation des Présences

⚠️ **Important** : Seules les présences avec `is_validated = true` sont comptabilisées dans les paies mensuelles.

### Workflow Recommandé

1. **En cours de mois** :
   - Vacataires scannent normalement
   - Admin valide les présences quotidiennement

2. **Fin de mois** :
   - Admin génère les paies
   - Vérifie et ajuste si nécessaire
   - Valide les paies
   - Effectue les paiements
   - Marque comme payé
   - Génère et remet les fiches de paie PDF

3. **Gestion contrats** :
   - Surveiller les alertes d'expiration
   - Renouveler les contrats à temps
   - Vérifier les quotas mensuels

## Tâches de Maintenance

### Tâche Cron Recommandée

Ajouter dans le scheduler Laravel :

```php
// app/Console/Kernel.php

protected function schedule(Schedule $schedule)
{
    // Vérifier les contrats expirés quotidiennement
    $schedule->call(function () {
        $service = new \App\Http\Services\VacataireServices();
        $service->checkExpiredContracts();
    })->daily();
}
```

## Support et Questions

Pour toute question ou problème :
1. Vérifier les logs Laravel : `storage/logs/laravel.log`
2. Vérifier la base de données pour les incohérences
3. S'assurer que toutes les migrations sont exécutées
4. Vérifier que DomPDF est installé pour les fiches de paie

## Prochaines Améliorations Possibles

- Export Excel des rapports (actuellement retourne JSON)
- Notifications automatiques aux vacataires
- Graphiques d'évolution dans les rapports
- Mode kiosque pour scan central (discuté mais reporté)
- API mobile pour consultation de paie
- Signature électronique sur les fiches de paie
