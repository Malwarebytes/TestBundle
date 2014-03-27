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

Because all our tests are database destructive, we use different database configurations for test, dev, and prod. We utilize parameters.yml to populate and keep track of the different DB configs. ```bin/testRunner setup``` sets the following:

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


If you would like to override this behavior and have your tests use the same database as prod/dev, simply change the ```force_different_test_db``` in config_test.yml:


```
testbundle:
    force_different_test_db: false
```

This will allow your test_db_* configs to be exactly the same as your database_* configs. You still need to explicitly define them though.
