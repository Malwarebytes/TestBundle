<?php
/**
 * User: Jonathan Chan <jchan@malwarebytes.org>
 * Date: 11/5/13
 * Time: 1:32 PM
 */

namespace Malwarebytes\TestBundle\Test;

use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\ORM\EntityManager;
use Malwarebytes\TestBundle\Event\ImportDataEvent;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application as App;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher;
use Symfony\Component\EventDispatcher\Event;

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
class DoctrineTransactionTestCase extends BaseWebTestCase
{

    /** @var  App */
    protected $application;
    /** @var  EntityManager */
    public $em;

    protected $firstRun;

    public function __construct()
    {
        $this->firstRun = true;
    }

    public function setUp()
    {
        parent::setUp();

        $this->em = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        if ($this->firstRun) {
            $this->firstRun = false;

            $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
            $tool->dropDatabase();

            $this->application = new App(self::$kernel);
            $this->application->add(new MigrateCommand());
            $this->application->setAutoExit(false);

            $input = new ArrayInput(array('command' => 'doctrine:migrations:migrate', '-q' => true, '-n' => true));
            $this->application->run($input, null);

            $conn = self::$kernel->getContainer()->get('doctrine.dbal.default_connection');

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

            $event = new ImportDataEvent(self::$kernel->getContainer());

            $dispatcher = new ContainerAwareEventDispatcher(self::$kernel->getContainer());
            $dispatcher->dispatch('malwarebytes.test.importdataevent', $event);
        }

        // Start transaction
        $this->em->getConnection()->beginTransaction();

    }

    public function tearDown()
    {
        // Rollback transaction
        $this->em->getConnection()->rollback();
        $this->em->getConnection()->close();
    }
}
