# Lost & Found module

PHP MVC module built like the `transport` and `fixmycity` modules:
`config/`, `models/`, `controllers/`, `views/frontoffice`, `views/backoffice`, `assets/`, `uploads/`.

Single entry point: `index.php?action=...`

## Database model

Based on the provided "Lost and Found - Database Model" screenshot:

- `categories(id_category, name, description)`
- `items(id_item, item_name, description, type, status, date_found, image_url, owner_id)`
- `messages(id_message, sender_id, receiver_id, message_text, date_sent, is_read)`

Two small foreign keys were added to make the drawn relationships work:
`items.id_category` and `messages.id_item`. Both point to the tables above.
`owner_id`, `sender_id` and `receiver_id` point to the shared `users` table.

## Run

1. Start Apache + MySQL in XAMPP.
2. Import `../fixmycity/config/fixmycity.sql` (creates the shared `fixmycity` database and `users`).
3. Import `config/lost_and_found.sql` (creates `categories`, `items`, `messages` + demo rows).
4. Open:
   - Front office: `http://localhost/<project-folder>/lost-and-found/index.php`
   - Back office: `http://localhost/<project-folder>/lost-and-found/index.php?action=admin_items`

The root navigation files `frontoffice/lost-and-found.php` and `backoffice/lost-and-found.php`
redirect to the module. The old `.html` mockups also redirect to those PHP files.

## Features

| | Front office | Back office |
|---|---|---|
| Items | Report, browse, filter, view details, edit/delete own pending item | View all, change status pending -> matched -> returned, delete |
| Categories | Read (select in form) | Create, update, delete (blocked if used) |
| Messages | Send a message about an item, view inbox | View all, mark as read |

Validation, prepared statements, CSRF tokens and upload checks live in `controllers/ItemController.php`.
Views only display escaped data.
