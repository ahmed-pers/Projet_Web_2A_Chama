# Smart Queue - PHP MVC CRUD

Application PHP simple (MVC + POO + PDO) avec **CRUD complet sur SERVICE et CATEGORIE**,
deux entités liées par `id_categorie` et affichées ensemble avec une **jointure SQL**
(`INNER JOIN`). Les TICKET sont créés dans le Front Office et affichés dans le Back Office.

## Schéma SQL utilisé

Le fichier `sql/schema.sql` contient uniquement les 3 tables fournies :

```
CATEGORIE (id_categorie, nomCategorie, description)
     ^
     | id_categorie
SERVICE (id_service, nomService, status, id_categorie)
     ^
     | id_service
TICKET (id_Ticket, id_client, id_service, date, time)
```

Les identifiants ne sont pas `AUTO_INCREMENT` : ils sont saisis dans les formulaires
`Ajouter`. Les colonnes `date` et `time` sont entourées de backticks dans les requêtes.

## Structure

```
index.php                       Contrôleur frontal (toutes les routes)
config/config.php               Paramètres BD + statuts autorisés
config/bootstrap.php            Session, autoload, helper e()
sql/schema.sql                  Vos 3 CREATE TABLE (exactement)
app/core/                       Database (PDO), Controller, Validator
app/models/                     Model, Categorie, Service, Ticket
app/controllers/                Front, BackCategorie, BackService, BackTicket, Home
app/views/                      layout + vues front/ et back/
public/js/validation.js         Validation JavaScript
```

## Installation

1. Installer PHP avec PDO MySQL et un serveur MySQL/MariaDB (XAMPP, WAMP, MAMP...).
2. Créer une base `smart_queue`, puis importer `sql/schema.sql` (phpMyAdmin ou ligne de commande).
3. Copier le projet dans le dossier du serveur web (par exemple `htdocs/smart-queue`).
4. Ajuster `config/config.php` ou utiliser les variables d'environnement
   `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.
5. Ouvrir `index.php` dans le navigateur.

## Routes

Tout passe par `index.php?page=...`.

### Front Office (création de ticket + lecture)

| Page | Description |
|---|---|
| `front` | Tableau de bord : compteurs, services ouverts |
| `front.services` | Services avec le nom de leur catégorie (jointure) |
| `front.tickets` | Formulaire de ticket + liste des tickets (jointure TICKET → SERVICE → CATEGORIE) |

### Back Office (CRUD complet)

| Entité | Afficher | Ajouter | Modifier | Supprimer |
|---|---|---|---|---|
| CATEGORIE | `back.categories` | `back.categorie.create` | `back.categorie.edit&id=1` | `back.categorie.delete` (POST `id`) |
| SERVICE | `back.services` | `back.service.create` | `back.service.edit&id=1` | `back.service.delete` (POST `id`) |

Tickets (lecture seule) : `back.tickets`.

## Validation

- **JavaScript** : `public/js/validation.js` vérifie les champs `data-required`
  dans les formulaires `data-validate`.
- **PHP** : `Validator` revérifie tout côté serveur (required, longueur, entier,
  valeur autorisée, unicité, format date/heure).

## Règles métiers

### Simples (implémentées)

1. `id_categorie` et `id_service` : entiers positifs, uniques.
2. `nomCategorie` et `nomService` : obligatoires, uniques, 3 à 100 caractères.
3. `description` : 255 caractères maximum.
4. `status` : `open` ou `closed` uniquement.
5. `id_Ticket` et `id_client` : entiers positifs ; `date` au format `YYYY-MM-DD`,
   `time` au format `HH:MM`.

### Avancées (implémentées)

1. Un ticket ne peut être créé que pour un service **ouvert**.
2. Un service doit appartenir à une catégorie **existante** (contrôle PHP + clé étrangère).
3. On ne peut pas supprimer une catégorie qui a encore des services.
4. On ne peut pas supprimer un service qui a encore des tickets.
5. Un même client ne peut pas prendre deux tickets pour le même service le même jour.

### Avancées (idées pour plus tard)

6. Priorité ( elderly / handicap / VIP ) dans l'ordre d'appel des tickets.
7. Temps d'attente estimé à partir de l'heure du ticket et du service.
8. Statut du ticket (`waiting`, `called`, `served`, `skipped`) avec historique.
9. Statistiques par catégorie : nombre de tickets, temps moyen d'attente.
