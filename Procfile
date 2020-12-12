web: composer warmup && vendor/bin/heroku-php-apache2 public/
release: php artisan migrate --force && php artisan cache:clear
