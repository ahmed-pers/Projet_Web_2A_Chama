# Smart Queue - version simple (PHP MVC + PDO)

Application simple avec vos 3 tables SQL. L'action principale du Front Office est
**Reserve** : le visiteur choisit un service et un numéro de client, et un ticket est ajouté
dans la table TICKET. Le Back Office gère les catégories et les services (CRUD complet).

## Tables utilisées (votre SQL exact)

```
CATEGORIE (id_categorie, nomCategorie, description)
SERVICE   (id_service, nomService, status, id_categorie)
TICKET    (id_Ticket, id_client, id_service, date, time)
```

`sql/schema.sql` contient uniquement ces 3 `CREATE TABLE`, sans modification.

Les numéros (`id_categorie`, `id_service`, `id_Ticket`) sont **générés automatiquement**
par l'application (`MAX(id) + 1`) : aucun champ de numéro dans les formulaires.
La date et l'heure du ticket sont également ajoutées automatiquement.

## Structure

```
index.php                    Contrôleur frontal
config/                      BD + statuts autorisés + autoload
sql/schema.sql               Vos 3 tables
app/core/                    Database (PDO), Controller, Validator
app/models/                  Model, Categorie, Service, Ticket
app/controllers/             Front, BackCategorie, BackService, BackTicket, Home
app/views/                   layout + vues front/ et back/
public/js/validation.js      Validation JavaScript
```

## Installation

1. PHP + PDO MySQL et MySQL/MariaDB (XAMPP, WAMP, MAMP...).
2. Créer la base `smart_queue` et importer `sql/schema.sql`.
3. Mettre le projet dans `htdocs` et ouvrir `index.php`.
4. Adapter `config/config.php` ou les variables `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`.

## Utilisation

### Front Office
- `front` : accueil avec lien **Reserve a ticket**.
- `front.tickets` : formulaire **Reserve** (client + service) et liste des tickets.
- `front.services` : services avec leur catégorie (jointure SQL).

### Back Office
- `back.categories` : afficher / ajouter / modifier / supprimer une catégorie.
- `back.services` : afficher / ajouter / modifier / supprimer un service.
- `back.tickets` : liste des tickets (lecture seule).

## Validation

- **JavaScript** : champs `data-required` dans les formulaires `data-validate`.
- **PHP** : `Validator` (obligatoire, longueur, entier, statut, unicité).

## Règles métiers

### Simples (implémentées)
1. Nom de catégorie et de service : obligatoire, unique, 3 à 100 caractères.
2. Description : 255 caractères maximum.
3. Statut d'un service : `open` ou `closed`.
4. Numéro de client : entier positif.

### Avancées (implémentées)
1. On ne peut réserver que pour un service **ouvert** et existant.
2. Un même client ne peut pas réserver deux fois pour le même service le même jour.
3. On ne peut pas supprimer une catégorie qui a encore des services.
4. On ne peut pas supprimer un service qui a encore des tickets.

### Avancées (idées pour plus tard)
5. Priorité (elderly / handicap / VIP) dans l'ordre d'appel.
6. Temps d'attente estimé à partir de l'heure du ticket.
7. Statut du ticket (`waiting`, `called`, `served`) et historique.
