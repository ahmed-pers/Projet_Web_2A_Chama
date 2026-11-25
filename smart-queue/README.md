# Smart Queue - version simple (MVC + PDO)

Projet simple avec vos 3 tables SQL, un bouton **Reserve** pour ajouter un ticket,
et le CRUD de CATEGORIE et SERVICE dans le Back Office.

Le code PHP est volontairement écrit de façon simple et lisible, comme un projet
d'étudiant : chargement des classes avec `require_once` dans `index.php`,
validations avec des `if`, pas d'autoload et pas de classe Validator.

## Structure du dépôt (comme les autres services)

```
index.html                     Page d'accueil du projet
frontoffice/smart-queue.html   Entrée Front Office
backoffice/smart-queue.html    Entrée Back Office
smart-queue/                   Application PHP (ce dossier)
fixmycity/ , transport/ ...    Projets des autres étudiants
```

Les deux fichiers HTML sont des pages autonomes avec la navigation
« Other services ». Ils renvoient vers l'application PHP dans `smart-queue/`.

## Tables (votre SQL, inchangé)

```
CATEGORIE (id_categorie, nomCategorie, description)
SERVICE   (id_service, nomService, status, id_categorie)
TICKET    (id_Ticket, id_client, id_service, date, time)
```

`sql/schema.sql` contient exactement vos 3 `CREATE TABLE`, sans données de test.
Les IDs et la date/heure des tickets sont générés par l'application.

## Fichiers de l'application

```
smart-queue/
  index.php               Chargement des classes + routes (if / elseif)
  config/config.php       Paramètres de la base de données
  sql/schema.sql          Vos 3 tables
  app/core/Database.php   Connexion PDO
  app/core/Controller.php render() et redirect()
  app/models/             Model, Categorie, Service, Ticket
  app/controllers/        Front + Back (validations simples)
  app/views/              layout + 8 vues
  public/css/style.css    Interface moderne en un seul fichier
  public/js/validation.js Validation JavaScript des formulaires
```

## Lancer avec XAMPP (Windows)

1. Copier **tout le dépôt** dans `C:\xampp\htdocs\Projet_Web_2A_Chama`
   (ou copier seulement le dossier `smart-queue` dans `C:\xampp\htdocs\smart-queue`).
2. Ouvrir `C:\xampp\xampp-control.exe` et démarrer **Apache** et **MySQL**.
3. Dans `http://localhost/phpmyadmin`, créer la base `smart_queue`,
   la sélectionner, onglet **Import** → `smart-queue/sql/schema.sql` → **Go**.
4. Ouvrir une des pages :

```
http://localhost/Projet_Web_2A_Chama/frontoffice/smart-queue.html
http://localhost/Projet_Web_2A_Chama/backoffice/smart-queue.html
http://localhost/Projet_Web_2A_Chama/smart-queue/index.php?page=front
```

Si vous avez copié uniquement le dossier `smart-queue` dans `htdocs`, l'URL est :

```
http://localhost/smart-queue/index.php?page=front
```

Ne pas double-cliquer sur `index.php` : c'est Apache qui doit exécuter le PHP.
Ajouter une catégorie, puis un service, puis réserver un ticket depuis le Front Office.

## Pages PHP

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
