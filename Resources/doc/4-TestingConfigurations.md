# Testing Configurations

## Separating Unit/Functional and Integration Tests

>
> OBSOLETE
> This section needs to be updated.
>

We separate our Integration Tests from our Unit/Functional Tests so that they do not have to be run everytime since they reach out to external services and tend to take much longer than the functional/unit tests. To achieve this functionality, create a ```Functional``` and ```Integration``` folder within your ```Tests``` folder and place the respective tests within the respective folders.

You may call the different test suites from command line via:


```bash
# Symfony Base Project
$ bin/testRunner unit # for running unit
$ bin/testRunner functional # for running functional tests
$ bin/testRunner integration # for running integration tests
```

## Protecting Production Database From Dev Database Resets

Because all our tests are database destructive, we use different database configurations for test, dev, and prod. We utilize parameters.yml to populate and keep track of the different DB configs. To use this functionality, change the following:

config.yml
```yml
doctrine:
    dbal:
        driver:   %database_driver%
        host:     %database_host%
        port:     %database_port%
        dbname:   %database_name%
        user:     %database_user%
        password: %database_password%
```


config_test.yml
```yml
doctrine:
    dbal:
        driver:   %test_db_driver%
        host:     %test_db_host%
        port:     %test_db_port%
        dbname:   %test_db_name%
        user:     %test_db_user%
        password: %test_db_password%
        path:     %test_db_path%
```

parameters.yml
```yml
parameters:
    database_driver:   pdo_mysql
    database_host:     localhost
    database_port:     ~
    database_name:     symfony
    database_user:     dev
    database_password: ~

    # used for functional / integration tests
    test_db_driver:    pdo_sqlite
    test_db_host:
    test_db_port:
    test_db_name:
    test_db_user:
    test_db_password:
    test_db_path:      %kernel.root_dir%/cache/test/data.sqlite
```

After this, we create our own test.bootstrap.php to check if the production database is the same as the testing database, and if so, prevent accidentally running the tests against the production database. To enable this, copy the test.bootstrap.php from the [example folder](ConfigExamples) and change the bootstrap config line in your phpunit* files to:

```xml
<phpunit
    ...
    bootstrap                   = "test.bootstrap.php" >
```


## Everything Together

Please refer to the [Configuration Examples](ConfigExamples) folder to see all these configuration files together.
