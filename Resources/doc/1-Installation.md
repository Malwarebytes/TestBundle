# Installation

To install the TestBundle, you may install it via composer or via git submodules. Composer is more convenient if you are just using the code, git submodules are more useful if you plan on contributing and updating the code often as you do not have to worry about keeping the composer.lock file up to date.

## Composer Install Instructions

>** NOTE/TODO: **
>
> When TestBundle goes public, add to packagist to simplify first step installation to:
> $ composer require malwarebytes/test-bundle:dev-master


1. Add the following to your composer.json file:


    ```
        "repositories": [
            ..
            {
                "type": "vcs",
                "url": "git@github.mb-internal.com:Webapps/TestBundle.git"
            }, 
           ..
        ],
        "require": {
            ..
            "malwarebytes/test-bundle": "dev-master",
            ..
        },
    ```


2. Run composer update:

    ```bash
    $ composer update malwarebytes/test-bundle
    ```

3. Enable the bundle within symfony:


    ``` php
    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
         $bundlles = array (
             // ...
             new Malwarebytes\AltamiraBundle\MalwarebytesTestBundle(),
         );
    }
    ```

## Git Submodule Install Instructions

1. Setup git submodules:

    ```bash
    $ git submodule add git@github.mb-internal.com:Webapps/TestBundle.git src/Malwarebytes/TestBundle
    $ git submodule update --init # any other commands?
    ```

2. Enable the bundle within symfony:


    ``` php
    <?php
    // app/AppKernel.php

    public function registerBundles()
    {
         $bundlles = array (
             // ...
             new Malwarebytes\AltamiraBundle\MalwarebytesTestBundle(),
         );
    }
    ```
