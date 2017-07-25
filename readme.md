# PHPFlip

PHPFlip is a standard project architecture. It requires PHP 7.1.

## Application Structure

- Core (root of everything)
    - Managers
        - OrderManager.php
    - Entities (Contains class that representate the business logic)
        - OrderEntity.php
        - UserEntity.php
    - Contracts
        - Repositories (Connection manager, interact with databases)
            - UserRepository.php
        - Models
            - User.php (Database representation, like DAO or POPO)
            - Token.php
        - Infrastructures (Mailer, Logger, etc)
    - Validators
        - Validator.php
        - Rules
            - Auth
                - LoginRules.php
                - ForgotRules.php
    - Util (Don't touch, unless you know how to defeat a dragon)
        - Presenters
            - XML.php
            - JSON.php
            - Protobuf.php
    - PubSub
        - Emitter.php
        - Publishers (It could be grouped per module if needed)
            - UserIsNotOnline.php
            - Users
                - UserHasBeenCreated.php (naming should be as detail as possible)
                - UserLostTheirPassword.php
            - Orders
                - OrderHasBeenMade.php
        - Subscribers
            - SendToEmail.php
    - Transformer
        - Transformer.php
        - Autobots
            - UserAutobot.php

## Guide

PHPFlip has a rigid rules about how to adding new components.

### Adding New Feature

Here's what you need to do if you want to add new feature:

1. You need to create `Manager`. Manager is responsible to managing resource, it has many "hands" to handle it's responsibilities. You also needs to create these "hands" too. These "hands" are:
    - `Repository`
        - `Model`
            - `Autobot`
    - `(Business)Entity`
    - `(Validator)Rules`
    - `(Event)Emitter` (Optional)
        - `Publisher`
        - `Subscriber`
    1.a. After you make a `Manager`. You have to make a `Repository`. A `Repository` is a class that responsible to interact with database (getting records or persisting records).
        1.a.1. Inside `Repository`, there's a `Model`. `Model` is a class that representates a database record. It's like a Plain Ol' file. You may add some extra contract to define some useful methods.
            1.a.1.a. A `Model` has it's own `Autobot`. `Autobot` is a file that responsible to convert a database record to a rigid structure that will be used for application response.
    1.b. Another class you need to make is an `Entity`. `Entity` is a class that contains business logic such as calculation method, creation method, or something else.
    1.c. You may need a `Rules` after then. Just like it's name, `Rules` is responsible to validate any operation, it only contains rulesets that any operation should pass before it can continue.
    1.d. You can make it more "separated" by making a event-driven class. Just make a `Publisher` (a class that defines an event name) that has one or many `Subscriber(s)` (a class that triggered when an event is happens). You have to trigger a `Publisher`, then any `Subscriber(s)` that listen to that `Publisher` will triggered based on it's priority.
2. **TESTING IS IMPORTANT**. Make sure you make a test case inside `core-tests` folder. Don't forget to full run unit-test, so you will notice if you make a breaking-changes when making a new feature.
3. Lastly, you need to fixing you code style to meet our specification. See *Code Fixing* section below.

### Installing

```sh
composer install

# we assume you have configured your .env file
php artisan migrate --seed
```

### Testing

```sh
composer global remove 'phpunit/phpunit' -vvv # do this if you have installed phpunit which not in version 5.0.*
composer global require 'phpunit/phpunit:^5.0' -vvv

# to test all suites
phpunit

# or for testing specific core package
phpunit --testsuite Core

# or for testing application
phpunit --testsuite App
```

### Code Fixing

```sh
composer global require friendsofphp/php-cs-fixer -vvv

# in root folder of this project
composer run-script cs-fixer
```

## TODO

- [ ] Unit testing
- [ ] Pagination response
- [ ] Improve `Core\Validator\Validator`
- [ ] Improve `Core\Contracts\Repositories\Repository`
