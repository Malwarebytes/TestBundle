<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 3/7/14
 * Time: 1:07 PM
 */

namespace Malwarebytes\TestBundle\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SetupCommand extends Command {
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('setup')
            ->setDescription('Configures the symfony project to use Malwarebytes Test Framework')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$name = $input->getArgument('name');
        $name = "world";
        if ($name) {
            $text = 'Hello '.$name;
        } else {
            $text = 'Hello';
        }

        $output->writeln($text);
    }

    /**
     * Checks if Malwarebytes TestBundle has been properly configured.
     *
     * TODO: implement me!
     *
     * @return bool
     */
    public static function isConfigured()
    {
        return true;
    }
} 