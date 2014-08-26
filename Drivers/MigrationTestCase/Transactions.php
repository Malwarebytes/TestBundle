<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 3/25/14
 * Time: 2:11 PM
 */

namespace Malwarebytes\TestBundle\Drivers\MigrationTestCase;
use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\ORM\EntityManager;
use Malwarebytes\TestBundle\Drivers\TestCaseDriver;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application as App;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Class DoctrineTransactionTestCase
 *
 * WebTestCase with Doctrine Migrations setup on first test run, then creating a transaction before and
 * then rolling back after each test is run.
 *
 * NOTE: This WebTestCase WILL DROP the DB when the first test is executed in any test file. Please run
 * this only against a dev DB
 *
 * Each test will have a clean migrated DB, useful for testing in a production like environment.
 *
 * @package Malwarebytes\TestBundle\Test
 */
class Transactions implements MigrationTestCaseDriver {
    static private $firstRun = true;

    /** @var  EntityManager */
    protected $em;
    protected $application;


    /**
     * Function called to setup database and/or environment for testing
     *
     * @param Client $client
     * @return null
     */
    public function setUp(Client $client)
    {
        $this->em = $client->getContainer()
            ->get('doctrine')
            ->getManager();

        if (self::$firstRun) {
            self::$firstRun = false;

            $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
            $tool->dropDatabase();

            $this->application = new App($client->getKernel());
            $this->application->add(new MigrateCommand());
            $this->application->setAutoExit(false);

            $input = new ArrayInput(array('command' => 'doctrine:migrations:migrate', '-q' => true, '-n' => true));
            $this->application->run($input, null);

            $conn = $client->getContainer()->get('doctrine.dbal.default_connection');

            // Actual code starts here
            $sql = "SHOW tables";
            $stmt = $conn->prepare($sql);
            $stmt->execute();

            $rows = array();
            while ($tbl = $stmt->fetchColumn()) {

                $sql = "ALTER TABLE $tbl ENGINE=INNODB";

                $stmt2 = $conn->prepare($sql);
                $stmt2->execute();
            }

            $event = $client->getContainer()->get('malwarebytes_test.post_schema_setup_event');
            $dispatcher = $client->getContainer()->get('event_dispatcher');
            $dispatcher->dispatch('malwarebytes_test.events.post_schema_setup', $event);
        }

        // Start transaction
        $this->em->getConnection()->beginTransaction();
    }

    /**
     * Function called to teardown database and/or reset the environment
     *
     * @param Client $client
     * @return null
     */
    public function tearDown(Client $client)
    {
        // Rollback transaction
        $this->em->getConnection()->rollback();
        $this->em->getConnection()->close();
    }
}