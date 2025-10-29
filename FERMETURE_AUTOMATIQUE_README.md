# Système de Fermeture Automatique des Présences

## 📋 Vue d'ensemble

Ce système ferme automatiquement à **minuit (00h00)** toutes les présences qui n'ont pas eu de check-out, en appliquant une **pénalité de demi-journée** pour les employés permanents et semi-permanents.

---

## ✨ Fonctionnalités

### 1. **Fermeture automatique à minuit**
- ✅ Détecte toutes les présences sans check-out de la veille
- ✅ Exclut automatiquement les **vacataires** (pas de check-out = pas de salaire)
- ✅ Applique une **demi-journée** (50% des heures du shift)
- ✅ Déduit **50% du salaire journalier**
- ✅ Marque la présence comme **`half_day`** et **`is_auto_closed`**

### 2. **Nouveau check-in le lendemain**
- ✅ Permet un nouveau check-in même si la journée précédente n'a pas été fermée
- ✅ Le système vérifie uniquement les présences **du jour même**

### 3. **Interface admin améliorée**
- ✅ Badge **"Demi-journée"** (jaune) pour les présences auto-fermées
- ✅ Badge **"En cours"** (bleu) pour les présences sans check-out
- ✅ Badge **"Complète"** (vert) pour les présences avec check-out

---

## 🗄️ Structure de la base de données

### Nouvelles colonnes dans `attendances`

| Colonne | Type | Description |
|---------|------|-------------|
| `is_auto_closed` | boolean | `true` si fermée automatiquement à minuit |
| `absence_penalty` | decimal(10,2) | Montant de la pénalité (50% du salaire) |
| `attendance_status` | enum | `'incomplete'`, `'half_day'`, `'on_time'`, `'late'`, `'early_departure'`, `'absent'` |

---

## ⚙️ Configuration du CRON

### Option 1 : CRON système (Recommandé pour production)

Ajoutez cette ligne dans votre crontab :

```bash
# Ouvrir l'éditeur crontab
crontab -e

# Ajouter cette ligne (remplacez le chemin par votre chemin absolu)
0 0 * * * cd /chemin/vers/digital-hr-admin-files && php artisan attendance:auto-close >> /dev/null 2>&1
```

### Option 2 : Laravel Scheduler (Déjà configuré)

Le scheduler Laravel est déjà configuré dans `app/Console/Kernel.php`. Il suffit d'ajouter cette ligne au crontab :

```bash
* * * * * cd /chemin/vers/digital-hr-admin-files && php artisan schedule:run >> /dev/null 2>&1
```

Ensuite, le système exécutera automatiquement la commande `attendance:auto-close` à minuit.

### Option 3 : Serveur de développement local

Pour tester localement sans attendre minuit, vous pouvez exécuter manuellement la commande :

```bash
# Fermer les présences d'une date spécifique
php artisan attendance:auto-close --date=2025-10-28

# Fermer les présences d'hier (par défaut)
php artisan attendance:auto-close
```

---

## 🧪 Test du système

### Test 1 : Vérifier que la commande fonctionne

```bash
php artisan attendance:auto-close --date=2025-10-29
```

**Résultat attendu :**
```
========================================
FERMETURE AUTOMATIQUE DES PRÉSENCES
========================================

Date traitée: 2025-10-29

Présences trouvées: 2

✓ John Doe (ID: 1)
  Shift: 480min → Demi-journée: 240min
  Salaire journalier normal: 5,000 FCFA
  Salaire demi-journée: 2,500 FCFA
  Pénalité absence check-out: 2,500 FCFA

⊘ Boussa Steve Junior (ID: 5) - VACATAIRE - Ignoré (pas de check-out = pas de salaire)

========================================
RÉSUMÉ
========================================
Présences fermées: 1
Vacataires ignorés: 1
Erreurs: 0

✅ Fermeture automatique terminée!
```

### Test 2 : Scénario complet

1. **Jour 1 (aujourd'hui)** - Faire un check-in pour un employé permanent :
   ```
   Aller sur : /admin/attendance
   Cliquer sur "Create Attendance Of employee"
   Sélectionner un employé permanent
   Faire un Check-In
   ```

2. **Jour 1 (fin de journée)** - NE PAS faire de check-out

3. **Minuit** - La commande CRON s'exécute automatiquement (ou manuellement) :
   ```bash
   php artisan attendance:auto-close
   ```

4. **Vérification** - Aller sur `/admin/attendance` et vérifier :
   - ✅ Badge **"Demi-journée"** affiché
   - ✅ `is_auto_closed = 1` dans la base de données
   - ✅ `attendance_status = 'half_day'`
   - ✅ `absence_penalty = 50% du salaire`
   - ✅ `total_working_duration = 50% du shift`

5. **Jour 2 (lendemain)** - Faire un nouveau check-in :
   ```
   Aller sur : /admin/attendance
   Faire un nouveau Check-In pour le même employé
   ```
   - ✅ Doit fonctionner sans erreur
   - ✅ Une nouvelle présence est créée pour le jour 2

---

## 📊 Logique de calcul

### Pour les permanents/semi-permanents :

```php
// Exemple : Shift de 8h (480 minutes)
$totalShiftMinutes = 480;
$halfDayMinutes = 480 / 2 = 240; // 4 heures

// Salaire mensuel = 220,000 FCFA
$monthlySalary = 220000;
$dailySalaryFull = 220000 / 22 = 10,000 FCFA; // ~22 jours ouvrables

// Demi-journée
$halfDaySalary = 10000 / 2 = 5,000 FCFA;
$penalty = 5,000 FCFA; // 50% de pénalité

// Mise à jour
is_auto_closed = true
attendance_status = 'half_day'
absence_penalty = 5000.00
total_working_duration = 240 (minutes)
daily_salary = 5000.00
```

### Pour les vacataires :

```php
// Ignorés par le système auto-close
// Pas de check-out = Pas de salaire = 0 FCFA
// Le système ne les traite pas
```

---

## 🔍 Vérification dans la base de données

```sql
-- Voir toutes les présences auto-fermées
SELECT
    u.name,
    a.date,
    a.check_in,
    a.check_out,
    a.is_auto_closed,
    a.attendance_status,
    a.total_working_duration,
    a.absence_penalty,
    a.daily_salary
FROM attendances a
JOIN users u ON u.id = a.user_id
WHERE a.is_auto_closed = 1
ORDER BY a.date DESC;
```

---

## 📝 Logs

Les logs de la commande CRON sont enregistrés dans :

```bash
storage/logs/laravel.log
```

Recherchez les entrées avec :
```
Auto-close attendances executed successfully
```
ou
```
Auto-close attendances failed
```

---

## 🚨 Cas particuliers

### Si un employé a fait check-in mais pas check-out :
- ✅ À minuit, le système ferme automatiquement la présence
- ✅ Marque comme "demi-journée"
- ✅ Applique 50% de pénalité

### Si l'admin veut corriger manuellement :
- ✅ Aller sur `/admin/attendance`
- ✅ Cliquer sur "Edit" pour la présence
- ✅ Ajouter manuellement le check-out
- ✅ Le statut sera mis à jour en "complete"

### Si un vacataire oublie de faire check-out :
- ✅ Le système l'ignore (pas d'auto-fermeture)
- ✅ Salaire = 0 FCFA (logique normale)
- ✅ L'admin peut ajouter manuellement le check-out plus tard

---

## 🎯 Avantages du système

1. **Automatisation complète** - Plus besoin de fermer manuellement les présences
2. **Équité** - Tous les employés sont traités de la même manière
3. **Pénalité claire** - Demi-journée = 50% de réduction
4. **Traçabilité** - Le flag `is_auto_closed` permet de savoir quelles présences ont été fermées automatiquement
5. **Flexibilité** - L'admin peut toujours corriger manuellement si nécessaire

---

## 🛠️ Dépannage

### La commande CRON ne s'exécute pas :
```bash
# Vérifier que le CRON est bien configuré
crontab -l

# Tester manuellement
php artisan attendance:auto-close --date=2025-10-29

# Vérifier les logs CRON
tail -f /var/log/cron.log  # Linux
tail -f /var/log/system.log  # macOS
```

### Les présences ne sont pas fermées :
```bash
# Vérifier les présences sans check-out
php artisan tinker
>>> \App\Models\Attendance::whereDate('check_in', '2025-10-29')->whereNull('check_out')->get();
```

### Erreur de timezone :
- Vérifier que le timezone est correct dans `config/app.php`
- Le système utilise `SetCompanyTimezone` middleware

---

## 📞 Support

Pour toute question ou problème, consultez :
- Les logs : `storage/logs/laravel.log`
- La documentation Laravel : https://laravel.com/docs/scheduling
- Le code de la commande : `app/Console/Commands/AutoCloseAttendances.php`
