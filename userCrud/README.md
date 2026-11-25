# FIXMYCITY – reports module

Citizens report a city problem (photo + location + category). The category routes the report
to a department. Stack: PHP (OOP, MVC, PDO), MySQL, HTML and CSS. Single entry point: `index.php?action=...`

## Run
1. Put this folder in `xampp/htdocs/userCrud`, start Apache + MySQL.
2. Import `config/fixmycity.sql` in phpMyAdmin (creates the DB, the tables, seed departments/categories and 2 dev users).
3. Put `logo.png` and `avatar.png` in `assets/imgs/`, your background images in `assets/backgrounds/`.
4. Open `http://localhost/userCrud/` (or `http://localhost/userCrud/index.php`) to sign in or create an account. The complete app, including its PHP entry point, controllers, models, views, configuration, and assets, is contained in this top-level `userCrud/` folder. Forms and CRUD actions are handled by PHP without JavaScript.
   The seeded accounts are `user1` / `admin1` with password `User@1234`.
   Admin reports: sign in as `admin1`, then open `http://localhost/userCrud/index.php?action=admin_reports`.
5. Password reset sends a one-hour link with PHP `mail()`. Configure a local mail transport for delivery;
   when developing on `localhost` without mail configured, the reset form opens directly after submission.

## Structure
- `index.php` session config, security headers, router
- `config/database.php` shared (Database::getConnexion()); `config/fixmycity.sql`
- `models/` ReportModel, CategoryModel (attributes + getters/setters + fromArray, no SQL)
- `controllers/ReportController.php` report/category logic; `controllers/AuthController.php` registration, login, logout and password reset
- `views/frontoffice`, `views/backoffice` display only, every value through htmlspecialchars()
- `assets/` css, imgs, backgrounds; `uploads/` report photos

## CRUD
| | Front office | Back office |
|---|---|---|
| Report | Create, Read (all / mine), Update + Delete own report while pending | Read all (join), Update status, Delete any |
| ProblemCategory | Read (select) | Create, Update, Delete (blocked if used) |
Department = seeded data (join: report -> problem_category -> department).

## Business rules
Simple: photo required (finfo, jpg/png/webp, 5 MB, random name); category must exist; description <= 500;
address OR coordinates in range; edit/delete only own reports while pending; category names unique;
a category with reports cannot be deleted.
Advanced: (1) status workflow pending -> in_progress -> resolved, no going back;
(2) automatic routing category -> department -> contact_email (shown on forms and in the back office);
(3) points and rank computed in PHP: 10 points per report in_progress/resolved, ranks by thresholds, no extra column.

`index.php` configures secure sessions, 30-minute inactivity expiry and security headers. The database schema
includes `password_reset` tokens. Re-import `config/fixmycity.sql` on an existing database to create that table.
