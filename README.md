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

---------------------------------------------------------------------------------------------------------------------------------
## Main improvements TODO
### Positions management
The current way to manage position is not the best. 
If two IP are added (1.1.1.1 and 1.1.1.2 for example), if they are in the same city (in the same country in the same region), the position object will be duplicated
So requests with position filtered will not be efficient.
To prevent API from duplicated position there are 2 solutions (one is better but more difficult to do)
-  Update initPositionFormWhoIsAPI() and add a verification to know if an object with same country/region/city already exist (and use it if it is the case)
-  Radically change the database structure IP contain a CITY that contain one REGION that contain one COUNTRY. 
   This solution looks the best because in data base you will have only one FranceObject one Bretagne even if you have Brest and Saint-Malo in your database. 
   But majority of request will have to be updated so this solution is more difficult to implement.

### Change some POST request to GET
We didn't had time to search how to pass parameter in GET request (to filter by target region for example).
So some existing request have been created as POST request with bodies (instead of GET request with parameters)
It should be change, that way we would have a clean rest API

### Correct setPrevious method (in PackagePassage)
This function partially doesn't work.
It should set the $previous value and the $previous->next value.
For now it only change the $previous value so $next is always null.
