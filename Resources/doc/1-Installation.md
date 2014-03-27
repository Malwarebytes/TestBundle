# Installation

Installing TestBundle is simple - we simply use Packagist/Composer.

## Composer Install Instructions



1. Run composer update:

    ```bash
    $ composer require malwarebytes/test-bundle
    ```

2. Enable the bundle within symfony in the dev/test section:


    ``` php
    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
        // ...
        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            // ...
            $bundles[] = new Malwarebytes\TestBundle\MalwarebytesTestBundle();
        }
    }
    ```

Note: Since this bundle is only used for tests, you can add it to the test/dev section instead of the main regular session.

3. Setup the config files:

    ```bash
    $ bin/testRunner setup
    ```



Refer to the next section on how to create proper tests.