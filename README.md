# Capitrain API

## Presentation / Context
Capitrain is a research project around the tracing of networks and the analysis of these data. 
This application is linked to two other projects: 
 -  a web UI (<https://github.com/lgillard/capitrain-IHM>) 
 -  an opensource software for tracing networks (<https://github.com/lgillard/capitrain-openvisualtraceroute>). 

---------------------------------------------------------------------------------------------------------------------------------
## Technologies
This application is an open API coded with Symfony 5.1

### API Platform
Api platform is a symfony bundle used to create rest API quickly.
It is based on Doctrine ORM bundle that allowed you to bind database into class.
 -  Documentation: <https://api-platform.com/docs/>
 
 ---------------------------------------------------------------------------------------------------------------------------------
## Required
! Before running the API make sure you have already installed:
 -  php (<https://www.php.net/manual/fr/install.php>)
 -  composer (<https://getcomposer.org/download/>)
 -  symfony (<https://symfony.com/download>)
 -  xampp or wampp (or any other web server) (<https://www.apachefriends.org/fr/download.html>)
 -  Mysql client (installed by default with xampp and wampp)
 
 After that you can clone the repository
> \> git clone <https://github.com/Zigotote/capitrain-api.git>

 Finally, please verify your .env configurations and make sure a MySql client is running.

---------------------------------------------------------------------------------------------------------------------------------
## Run the application
Install dependencies
> \> composer install

Create the database
> \> php bin/console doctrine:database:create or php bin/console d:d:c

Update the database
> \> php bin/console doctrine:schema:update --force or php bin/console d:s:u --force

Run the application
> \> symfony server:start

Now your API is available at this address <http://127.0.0.1:8000/api>

---------------------------------------------------------------------------------------------------------------------------------
##API UI mode
! Be careful, the java software is not compatible with API in UI mode. Before running it please make sure UI mode is deactivated.

If you want to access to API Platform API UI you can go to config/packages/api_platform.yaml and comment line 6 to 19
UI will be available at <http://127.0.0.1:8000/api>

Requests documentation is available on dedicated controllers

---------------------------------------------------------------------------------------------------------------------------------
## Tests
If you want some test data you can run capitrain.sql script (drop your database before running or execution might failed). 
The script will load some data for your tests. 
If you want more information about those data please take a look at schema_data_test.png
