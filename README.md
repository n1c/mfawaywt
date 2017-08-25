# Setup

```ssh
composer install
php artisan key:generate 
cp .env.example .env
vagrant up
vagrant ssh
cd /vagrant
php artisan migrate
```

Make sure to setup the relevant cron so the schedule happens, [docs](https://laravel.com/docs/master/scheduling#introduction).

There's a few artisan commands under the "app" namespace that could be of interest `php artisan app` will list them.
