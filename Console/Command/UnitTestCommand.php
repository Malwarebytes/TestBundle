<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 3/7/14
 * Time: 2:44 PM
 */

namespace Malwarebytes\TestBundle\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class UnitTestCommand  extends Command {
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('unit')
            ->setDescription("Runs unit tests; all options/arguments are passed to phpunit command")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $outputText = "NOTE: this command is not actually used to run anything - we only use it for making the console help.";

        $output->writeln($outputText);
    }
} 