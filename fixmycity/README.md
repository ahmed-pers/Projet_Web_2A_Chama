# FIXMYCITY – reports module

Citizens report a city problem (photo + location + category). The category routes the report
to a department. Stack: PHP (OOP, MVC, PDO), MySQL, HTML/CSS/JS. Single entry point: `index.php?action=...`

## Run
1. Put the folder in `xampp/htdocs/fixmycity`, start Apache + MySQL.
2. Import `config/fixmycity.sql` in phpMyAdmin (creates the DB, the tables, seed departments/categories and 2 dev users).
3. Put `logo.png` and `avatar.png` in `assets/imgs/`, your background images in `assets/backgrounds/`.
4. Open http://localhost/fixmycity/index.php  (front office)
   Back office: set `DEV_ROLE` to `'admin'` in `controllers/AuthController.php` (stub), then
   http://localhost/fixmycity/index.php?action=admin_reports

## Structure
- `index.php` session config, security headers, router
- `config/database.php` shared (Database::getConnexion()); `config/fixmycity.sql`
- `models/` ReportModel, CategoryModel (attributes + getters/setters + fromArray, no SQL)
- `controllers/ReportController.php` all logic; `controllers/AuthController.php` = STUB of the teammate's class
- `views/frontoffice`, `views/backoffice` display only, every value through htmlspecialchars()
- `assets/` css, js, imgs, backgrounds; `uploads/` report photos

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

## Merging with the auth teammate
- Replace `controllers/AuthController.php` (stub) with theirs. Same method names: `isAuthenticated`, `requireAuth`,
  `requireRole`, `getCsrfToken`. Add their cases (home, register, login, logout, dashboard, admin) in `index.php`.
- `index.php` already has their session config, 30 min inactivity expiry and security headers.
- The CSP only allows scripts from `'self'`: no inline `<script>` or `onclick` in the views.
- The sidebar "Log out" link points to their `logout` action (404 until merged).
- DB: `report.user_id` references their `users(id)`.
