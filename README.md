# Smart Queue - PHP MVC CRUD

A small PHP application that manages **services** and **counters (guichets)** linked by a
join table. It uses PDO, an MVC structure, object-oriented PHP, and simple beginner-friendly
HTML views.

## Entities and join table

```
services  <--->  service_counter  <--->  counters
```

- One service can be served by several counters.
- One counter can serve several services (maximum 3).
- `service_counter` has a composite primary key `(service_id, counter_id)` and two foreign
  keys with `ON DELETE CASCADE`.

## Project structure

```
index.php                  Front controller (all requests go through here)
config/
  config.php               Database settings + business rule constants
  bootstrap.php            Session, autoloader, e() helper
sql/
  schema.sql               Tables and sample data
app/
  core/
    Database.php           PDO connection (singleton)
    Controller.php         render(), redirect(), flash(), request helpers
    Validator.php          PHP form validation
  models/
    Model.php              Base model (all(), find(), delete())
    Service.php            Service CRUD
    Counter.php            Counter CRUD
    ServiceCounter.php     Join table operations
  controllers/
    FrontController.php    Front Office (read only)
    BackServiceController.php   Back Office services + assignments
    BackCounterController.php   Back Office counters
    HomeController.php     Default redirect
  views/
    layout.php             Shared page layout + navigation
    front/                 Front Office views
    back/                  Back Office views
public/js/
  validation.js            JavaScript form validation
```

## Installation

1. Install PHP 7.4+ with the PDO MySQL driver and a MySQL/MariaDB server.
   (For example with XAMPP, WAMP, MAMP, or Docker.)
2. Create the database and tables:
   ```bash
   mysql -u root -p < sql/schema.sql
   ```
   Or import `sql/schema.sql` in phpMyAdmin.
3. Copy the project into your web server root (for example `htdocs/smart-queue`).
4. Adjust the database settings in `config/config.php`, or use environment variables:
   ```
   DB_HOST, DB_NAME, DB_USER, DB_PASS
   ```
5. Open `index.php` in your browser.

## Routes

All actions use `index.php?page=...`.

### Front Office (read only)

| Page | Description |
|---|---|
| `front` | Dashboard: services and open counters |
| `front.services` | Services with their assigned counters |

### Back Office (full CRUD)

| Page | Description |
|---|---|
| `back.services` | List, add, edit, delete services |
| `back.service.create` | Create a service |
| `back.service.edit&id=1` | Edit a service |
| `back.service.delete` | Delete a service (POST with `id`) |
| `back.assign&id=1` | Assign counters to a service |
| `back.counters` | List, add, edit, delete counters |
| `back.counter.create` | Create a counter |
| `back.counter.edit&id=1` | Edit a counter |
| `back.counter.delete` | Delete a counter (POST with `id`) |

## Validation

- **JavaScript** (`public/js/validation.js`): checks required fields marked with
  `data-required` inside forms marked with `data-validate`.
- **PHP** (`Validator`): checks the same data again on the server (never trust the browser).

## Business rules

### Simple rules (implemented)

1. A service name is required, unique, and 3 to 50 characters long.
2. A service average time must be a number between 1 and 120 minutes.
3. A service status must be `open` or `closed`.
4. A counter number is required, unique, and 2 to 20 characters long.
5. A counter status must be `open` or `closed`.

### Advanced rules (implemented)

1. A **closed counter** cannot be assigned to a service.
2. A counter can serve **at most 3 services** (`MAX_SERVICES_PER_COUNTER` in `config/config.php`).
3. A counter that is the **only counter of an open service** cannot be deleted.

### Advanced rules (ideas for later)

4. Prevent deleting a service that still has waiting tickets.
5. A ticket can only be called if its service is open and has at least one open counter.
6. Only one active ticket per phone number at a time.
7. VIP / elderly tickets are served first inside the same service.
