## Project Setup

- composer install (If you are running PHP version greater than 8.4, then use 'composer update')
- cp .env.example .env
- Create database in your local phpmyadmin
- Update the DB configurations
```
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=database that you have created
DB_USERNAME=root
DB_PASSWORD=
```
- php artisan key:generate
- php artisan serve
- You can access your application using http://127.0.0.1:8000 url


#### application start process
- Login page for admin http://{APP_URL}/login
- after successfully login you will be redirected to a static dashboard page
