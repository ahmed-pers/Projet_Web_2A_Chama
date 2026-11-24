# Smart Queue - version simple (MVC + PDO)

Projet simple avec vos 3 tables SQL, un bouton **Reserve** pour ajouter un ticket,
et le CRUD de CATEGORIE et SERVICE dans le Back Office.

Le code PHP est volontairement écrit de façon simple et lisible, comme un projet
d'étudiant : chargement des classes avec `require_once` dans `index.php`,
validations avec des `if`, pas d'autoload et pas de classe Validator.

## Tables (votre SQL, inchangé)

```
CATEGORIE (id_categorie, nomCategorie, description)
SERVICE   (id_service, nomService, status, id_categorie)
TICKET    (id_Ticket, id_client, id_service, date, time)
```

`sql/schema.sql` contient exactement vos 3 `CREATE TABLE`, sans données de test.
Les IDs et la date/heure des tickets sont générés par l'application.

## Fichiers

```
index.php                  Chargement des classes + routes (if / elseif)
config/config.php          Paramètres de la base de données
sql/schema.sql             Vos 3 tables
app/core/Database.php      Connexion PDO
app/core/Controller.php    render() et redirect()
app/models/Model.php       all(), find(), nextId(), delete()
app/models/Categorie.php   Requêtes CATEGORIE
app/models/Service.php     Requêtes SERVICE
app/models/Ticket.php      Requête TICKET
app/controllers/           Front + Back (validations simples)
app/views/                 layout + 8 vues
public/css/style.css       Interface moderne en un seul fichier
public/js/validation.js    Validation JavaScript des formulaires
```

## Installation

1. Créer la base `smart_queue` et importer `sql/schema.sql`.
2. Adapter `config/config.php` si besoin (mot de passe MySQL, nom de la base...).
3. Ouvrir le projet avec un serveur PHP (XAMPP ci-dessous).

## Lancer avec XAMPP (Windows)

1. Copier le projet dans `C:\xampp\htdocs\smart-queue`.
2. Ouvrir `C:\xampp\xampp-control.exe` et démarrer **Apache** et **MySQL**.
3. Dans `http://localhost/phpmyadmin`, créer la base `smart_queue`,
   la sélectionner, onglet **Import** → `sql/schema.sql` → **Go**.
4. Ouvrir :

```
http://localhost/smart-queue/index.php?page=front
```

Ne pas double-cliquer sur `index.php` : c'est Apache qui doit exécuter le PHP.
Ajouter une catégorie, puis un service, puis réserver un ticket depuis le Front Office.

## Pages

| Page | Rôle |
|---|---|
| `front` | Accueil + statistiques |
| `front.services` | Liste des services |
| `front.tickets` | **Reserve** (client + service) + liste des tickets |
| `back.categories` | CRUD catégories |
| `back.services` | CRUD services |
| `back.tickets` | Liste des tickets (lecture seule) |

## Interface et validation

- `public/css/style.css` : barre sombre, cartes, statistiques, badges de statut,
  tableaux et boutons ; l'interface s'adapte au mobile.
- `public/js/validation.js` : vérifie les champs `data-required` avant l'envoi.
- Le PHP refait les mêmes vérifications avec de simples `if`.

Règles gardées :
- Client et service obligatoires et numériques.
- Nom de catégorie / service : obligatoire, unique, 3 à 100 caractères.
- Statut : `open` ou `closed`.
- Suppression bloquée si une catégorie a des services ou si un service a des tickets.
