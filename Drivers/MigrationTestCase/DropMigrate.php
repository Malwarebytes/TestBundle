<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 3/25/14
 * Time: 11:28 AM
 */

namespace Malwarebytes\TestBundle\Drivers\MigrationTestCase;


use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application as App;
use Symfony\Component\Console\Input\ArrayInput;

class DropMigrate implements MigrationTestCaseDriver {
    /** @var  EntityManager */
    private $em;
    /** @var  App */
    private $application;

    /**
     * Function called to setup database and/or environment for testing
     *
     * True return value will trigger a PostSchemaSetup event. False suppresses the event.
     *
     * @param Client $client
     * @return boolean
     */
    public function setUp(Client $client)
    {
        $kernel = $client->getKernel();
        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->dropDatabase();

        $this->application = new App($kernel);
        $this->application->add(new MigrateCommand());
        $this->application->setAutoExit(false);


        $input = new ArrayInput(array('command' => 'doctrine:migrations:migrate', '-q' => true, '-n' => true));
        $this->application->run($input, null);

        return true;
    }

    /**
     * Function called to teardown database and/or reset the environment
     *
     * @param Client $client
     * @return null
     */
    public function tearDown(Client $client)
    {
        // do nothing.
    }
}