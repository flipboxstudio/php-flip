# PHPFlip

PHPFlip is a standard project architecture. It requires PHP 7.

### Application Structure

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
            - HAL.php
            - JSON.php
            - BSON.php
            - Protobuf.php
    - PubSub
        - Emitter.php
        - Publishers (It could be grouped per module if needed)
            - CourierIsNotOnline.php
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

### TODO

- [ ] Unit testing
- [ ] Pagination response
- [ ] Improve `Core\Validator\Validator`
- [ ] Improve `Core\Contracts\Repositories\Repository`