1. git init
2. git remote add origin https://github.com/VipinFarswan/Shop.git
3. git config user.name 'your-name'
4. git config user.email 'your-email'
5. git checkout -b 'new-branch-name'  { eg. git checkout -b suraj } 
6. git pull origin developement
7. copy .env.eaxmple as .env
8. set DB_DATABASE and APP_TIMEZONE in .env file  {eg. DB_DATABASE=db_shop, APP_TIMEZONE=Asia/Kolkata} 
9.  composer install
10. php artisan key:generate
11. php artisan migrate
12. php artisan passport:install
13. php artisan db:seed {this will register a user with email:shop@disc-in.com and password 123456 }
