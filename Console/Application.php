<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 3/7/14
 * Time: 1:03 PM
 */

namespace Malwarebytes\TestBundle\Console;

use Malwarebytes\TestBundle\Console\Command\FunctionalTestCommand;
use Malwarebytes\TestBundle\Console\Command\IntegrationTestCommand;
use Malwarebytes\TestBundle\Console\Command\SetupCommand;
use Malwarebytes\TestBundle\Console\Command\UnitTestCommand;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication {
    /**
     * Constructor.
     */
    public function __construct()
    {
        error_reporting(-1);

        parent::__construct('Malwarebytes Test Runner', "0.1Beta");

        $this->add(new SetupCommand());
        $this->add(new UnitTestCommand());
        $this->add(new FunctionalTestCommand());
        $this->add(new IntegrationTestCommand());
    }
} 