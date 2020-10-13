# Nginx PHP MySQL

Docker running Nginx, PHP-FPM, Composer, MySQL.

## Overview

1. [Install prerequisites](#install-prerequisites)

    Before installing project make sure the following prerequisites have been met.

2. [Clone the project](#clone-the-project)

    We’ll download the code from its repository on GitHub.

3. [Run the application](#run-the-application)

    By this point we’ll have all the project pieces in place.

4. [Testing the Application](#testing-the-application)

    Curl commands to test the API

5. [Use Docker Commands](#use-docker-commands)

    When running, you can use docker commands for doing recurrent operations.

___

## Install prerequisites

To run the docker commands without using **sudo** you must add the **docker** group to **your-user**:

```
sudo usermod -aG docker your-user
```

For now, this project has been mainly created for Unix `(Linux/MacOS)`. Perhaps it could work on Windows.

All requisites should be available for your distribution. The most important are :

* [Git](https://git-scm.com/downloads)
* [Docker](https://docs.docker.com/engine/installation/)
* [Docker Compose](https://docs.docker.com/compose/install/)

Check if `docker-compose` is already installed by entering the following command :

```sh
which docker-compose
```

Check Docker Compose compatibility :

* [Compose file version 3 reference](https://docs.docker.com/compose/compose-file/)

On Ubuntu and Debian these are available in the meta-package build-essential. On other distributions, you may need to install the GNU C++ compiler separately.

```sh
sudo apt install build-essential
```

### Images to use

* [Nginx](https://hub.docker.com/_/nginx/)
* [MySQL](https://hub.docker.com/_/mysql/)
* [PHP-FPM](https://hub.docker.com/r/nanoninja/php-fpm/)
* [Composer](https://hub.docker.com/_/composer/)
* [Generate Certificate](https://hub.docker.com/r/jacoelho/generate-certificate/)

You should be careful when installing third party web servers such as MySQL or Nginx.

This project use the following ports :

| Server     | Port |
|------------|------|
| MySQL      | 3060 |
| Nginx      | 80   |

___

## Clone the project

To install [Git](http://git-scm.com/book/en/v2/Getting-Started-Installing-Git), download it and install following the instructions :

```sh
git clone https://github.com/jslmariano/php-oop-docker-nginx-mysql.git
```

Go to the project directory :

```sh
cd php-oop-docker-nginx-mysql
```

### Project tree

```sh
.
├── Makefile
├── README.md
├── mysql
│   └── db
│       └── dump.sql
├── doc
├── docker-compose.yml
├── etc
│   ├── nginx
│   │   ├── default.conf
│   │   └── default.template.conf
│   ├── php
│   │   └── php.ini
│   └── ssl
└── web
    ├── app
    │   ├── composer.json.dist
    │   ├── phpunit.xml.dist
    │   ├── report
    │   ├── src
    |   │   |── Configs/*
    |   │   |── Controllers/*
    |   │   |── Helpers/*
    |   │   |── Models/*
    │   │   ├── Foo.php
    │   │   └── functions.php
    │   └── test
    |       |── Configs/*
    |       |── Controllers/*
    |       |── Helpers/*
    |       |── Models/*
    │       ├── FooTest.php
    │       └── bootstrap.php
    └── public
        └── index.php
```

___

## Run the application

1. Copying the composer configuration file :

    ```sh
    cp web/app/composer.json.dist web/app/composer.json
    ```

2. Start the application :

    ```sh
    docker-compose up -d
    ```

    **Please wait this might take a several minutes...**

    ```sh
    docker-compose logs -f # Follow log output
    ```

3. Restore Dummy Database :

    ```
    source .env && docker exec -i $(docker-compose ps -q mysql) mysql -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD" < "mysql/db/dump.sql"

    ```

4. Open your favorite browser :

    * [http://localhost/tickets/cancel?ticketID=86](http://localhost/tickets/cancel?ticketID=86)

5. Run Tests with code coverage

    **Copy the PHPUNIT configuration**
    
    ```sh
    cp web/app/composer.json.dist web/app/composer.json
    ```
    
    **Run the phpunit test cli with code coverage**
    
    ```sh
    docker-compose exec -T php ./app/vendor/bin/phpunit --colors=always --configuration ./app
    ```

    **After tests is complete, code coverage is located in**

    ```sh
    ./app/web/report/phpunit/inedx.html
    ```

6. Stop and clear services

    ```sh
    docker-compose down -v
    ```

___

## Testing the Application

A set of curl commands to test out the API

* Testing the ticket confirmation

```curl
curl --location --request POST 'http://localhost/ticket/confirm?operatorSessionID=123456' \
--form 'ticket=[{"outcome":"Number 6","bet":"500.5","gameRoundID":"3s"},{"outcome":"Number 6","bet":500,"gameRoundID":3}]'
```

* Testing the ticket cancellation

```curl
curl --location --request GET 'http://localhost/ticket/cancel/?ticketID=xxx'
```
___

## Use Docker commands

### Installing package with composer

```sh
docker run --rm -v $(pwd)/web/app:/app composer require symfony/yaml
```

### Updating PHP dependencies with composer

```sh
docker run --rm -v $(pwd)/web/app:/app composer update
```

### Generating PHP API documentation

```sh
docker run --rm -v $(pwd):/data phpdoc/phpdoc -i=vendor/ -d /data/web/app/src -t /data/web/app/doc
```

### Testing PHP application with PHPUnit

```sh
docker-compose exec -T php ./app/vendor/bin/phpunit --colors=always --configuration ./app
```

### Checking installed PHP extensions

```sh
docker-compose exec php php -m
```

### Handling database

#### MySQL shell access

```sh
docker exec -it mysql bash
```

and

```sh
mysql -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD"
```

#### Creating a backup of all databases

```sh
mkdir -p data/db/dumps
```

```sh
source .env && docker exec $(docker-compose ps -q mysql) mysqldump --all-databases -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD" > "data/db/dumps/db.sql"
```

#### Restoring a backup of all databases

```sh
source .env && docker exec -i $(docker-compose ps -q mysql) mysql -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD" < "data/db/dumps/db.sql"
```

#### Creating a backup of single database

**`Notice:`** Replace "YOUR_DB_NAME" by your custom name.

```sh
source .env && docker exec $(docker-compose ps -q mysql) mysqldump -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD" --databases YOUR_DB_NAME > "data/db/dumps/YOUR_DB_NAME_dump.sql"
```

#### Restoring a backup of single database

```sh
source .env && docker exec -i $(docker-compose ps -q mysql) mysql -u"$MYSQL_ROOT_USER" -p"$MYSQL_ROOT_PASSWORD" < "data/db/dumps/YOUR_DB_NAME_dump.sql"
```

