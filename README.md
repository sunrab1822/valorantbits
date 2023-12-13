## Setup

1. `composer install`
2. `npm install`
3. Set database and app key in environment file
4. `php aritsan migrate`
5. `npm run dev`
6. `php artisan websocket:serve`
7. `php artisan queue:work`
8. `php artisan queue:work --queue=battle`

## Commands in order

1. `php artisan db:seed`
2. `php artisan valo:items`
3. `php artisan valo:price`
4. `php artisan valo:crates`
