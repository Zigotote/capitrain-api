# Capitrain API

_Intro :_

This application is an open API coded with Symfony 5.1

_Running the application:_

! Before running the API make sure you have already install php, composer and symfony (https://symfony.com/download). Furthermore, please verify yyour .env configuration and make sure a MySql client is running.

> \> git clone <https://github.com/Zigotote/capitrain-api.git>

> \> composer install

> \> php bin/console doctrine:database:create or php bin/console d:d:c

> \> php bin/console doctrine:schema:update --force or php bin/console d:s:u --force

> \> symfony server:start

Now you can access to you API at this address <http://127.0.0.1:8000/api>
