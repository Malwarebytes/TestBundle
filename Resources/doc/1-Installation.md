# Installation

Installing TestBundle is simple - we simply use Packagist/Composer.

## Composer Install Instructions



1. Run composer update:

    ```bash
    $ composer require malwarebytes/test-bundle
    ```

2. Enable the bundle within symfony:


    ``` php
    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
         $bundles = array (
             // ...
             new Malwarebytes\TestBundle\MalwarebytesTestBundle(),
         );
    }
    ```

3. Setup the config files:

    ```bash
    $ bin/testRunner setup
    ```



Refer to the next section on how to create proper tests.