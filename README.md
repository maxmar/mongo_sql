# MongoDB SQL Client

This test application provide feature to use SQL `SELECT` operator with MongoDB.

## Requirements

* PHP 7.0
* MongoDB driver (See [MongoDB PHP Driver Installation](http://php.net/manual/en/mongodb.installation.pecl.php))
* `composer` command (See [Composer Installation](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-osx))

## Installation

```sh
$ composer install
```

Above command install dependencies and prepare environment for testing.

To configure MongoDB connection you need to rename `app/config/config.yml.dist` to `app/config/config.yml` and put into this file your credentials:
```
mongodb:
    username: '[username]'
    password: '[password]'
    host:     'localhost'
    port:     27017
    database: '[database]'
```

## Usage

```sh
# From project root execute command
$ php mongo-sql

# Get data from collection
> SELECT * FROM collection_name;

# Select specific fields(templates: *, field, field.subfield, field.*) separated by commas
> SELECT field FROM collection_name;

# Use WHERE condition to extract only those records that fulfill a specified condition
> SELECT field FROM collection_name WHERE size > 10;

# Sort result
> SELECT field FROM collection_name ORDER BY age DESC, name;

# Use SKIP to control where MongoDB begins returning results
> SELECT field FROM collection_name SKIP 3;

# Use LIMIT to specify the maximum number of documents the cursor will return.
> SELECT field FROM collection_name LIMIT 5;
```

## Dependencies

- [mongodb](https://packagist.org/packages/mongodb/mongodb): MongoDB driver library
- [php-di](http://php-di.org/): Dependency Injection Container for PHP
- [yaml](https://packagist.org/packages/symfony/yaml): Symfony Yaml Component for pretty config files

## Dev Dependencies

- [phpunit](https://phpunit.de/): Testing framework for PHP

## Tests

```sh
$ phpunit
```
