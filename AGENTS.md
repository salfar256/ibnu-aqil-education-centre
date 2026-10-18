# Ibnu Aqil Education Centre
Laravel 13 / PHP >=8.3, MySQL or MariaDB; server rendered Blade and buildless CSS/JS.
Run composer install, configure .env, php artisan key:generate, php artisan migrate --seed.
Run php artisan test. DemoSeeder is local/testing only. No fixed credentials.
Protect every private file through auth + active account + MaterialAccess. Never create a public storage symlink.
Only admin manages users, schools, grants and public CMS. Editors only manage activities/materials.
Public content needs verified_at AND published status. Do not publish stale contacts, financial details or unsupported claims.
Do not include .env, SQLite DBs, private uploads, logs, cached PHP views, runtime tools or test credentials in Git or release packages.
GitHub Pages exports public pages only; PHP portal runs on Nimbus. No DNS or external publishing without user instruction.
