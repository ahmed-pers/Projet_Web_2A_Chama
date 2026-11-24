# Smart Queue - version simple (MVC + PDO)

Projet minimal : vos 3 tables SQL, un bouton **Reserve** qui ajoute un ticket,
et le CRUD de CATEGORIE et SERVICE dans le Back Office.

## Tables (votre SQL, inchangé)

```
CATEGORIE (id_categorie, nomCategorie, description)
SERVICE   (id_service, nomService, status, id_categorie)
TICKET    (id_Ticket, id_client, id_service, date, time)
```

`sql/schema.sql` = exactement vos 3 `CREATE TABLE`.
Les IDs et la date/heure des tickets sont générés automatiquement par l'application.

## Fichiers

```
index.php                  Routes
config/config.php          Connexion BD
config/bootstrap.php       Autoload des classes
sql/schema.sql             Vos tables
app/core/                  Database, Controller, Validator
app/models/                Model, Categorie, Service, Ticket
app/controllers/           FrontController, BackCategorieController,
                           BackServiceController, BackTicketController
app/views/                 layout + 8 vues
public/css/style.css       Style simple
public/js/validation.js    Validation JavaScript
```

## Installation

1. Créer la base `smart_queue` et importer `sql/schema.sql`.
2. Adapter `config/config.php` (ou `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).
3. Ouvrir `index.php` via un serveur PHP (voir XAMPP ci-dessous).

## Lancer avec XAMPP (Windows)

1. Copier le projet dans `C:\xampp\htdocs\smart-queue`.
2. Ouvrir `C:\xampp\xampp-control.exe`, puis cliquer **Start** sur **Apache** et **MySQL**.
3. Dans `http://localhost/phpmyadmin`, créer la base `smart_queue`,
   la sélectionner, onglet **Import** → `sql/schema.sql` → **Go**.
4. Ouvrir :

```
http://localhost/smart-queue/index.php?page=front
```

Ne pas double-cliquer sur `index.php` : Apache doit exécuter le PHP.
Les listes sont vides au départ : ajouter une catégorie, puis un service,
puis réserver un ticket.

## Pages

| Page | Rôle |
|---|---|
| `front` | Accueil + lien Reserve |
| `front.services` | Liste des services |
| `front.tickets` | **Reserve** (client + service) + liste des tickets |
| `back.categories` | CRUD catégories |
| `back.services` | CRUD services |
| `back.tickets` | Liste des tickets |

## Style et validation

- `public/css/style.css` : interface moderne en un seul fichier (barre sombre, cartes,
  statistiques, badges de statut, tableaux et boutons), responsive sur mobile.
- `public/js/validation.js` : vérifie les champs `data-required` des formulaires
  `data-validate` avant l'envoi, puis affiche une alerte.
- Le contrôle PHP (`Validator`) refait les mêmes vérifications côté serveur.

- Client et service obligatoires et numériques.
- Nom de catégorie / service : obligatoire, unique, 3 à 100 caractères.
- Statut : `open` ou `closed`.
- Suppression bloquée si une catégorie a des services ou si un service a des tickets.
