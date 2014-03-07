<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 3/7/14
 * Time: 2:45 PM
 */

namespace Malwarebytes\TestBundle\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class IntegrationTestCommand  extends Command {
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('integration')
            ->setDescription("Runs integration tests; all options/arguments are passed to phpunit command")
        ;
    }
} 