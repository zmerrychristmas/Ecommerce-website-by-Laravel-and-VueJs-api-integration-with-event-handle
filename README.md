 3728  docker-compose exec app bash
 3729  docker-compose exec db bash
 docker-compose exec app php artisan key:generate
 docker-compose exec app php artisan migrate --seed
 docker-compose exec app ./install.sh
php artisan queue:work redis --queue=flashsale --sleep=3 --tries=3 --max-time=3600