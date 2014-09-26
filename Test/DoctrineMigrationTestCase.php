<?php
/**
 * User: Jonathan Chan <jchan@malwarebytes.org>
 * Date: 11/5/13
 * Time: 1:32 PM
 */


namespace Malwarebytes\TestBundle\Test;


use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\ORM\EntityManager;
use Malwarebytes\TestBundle\Drivers\MigrationTestCase\MigrationTestCaseDriver;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Console\Application as App;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * Class DoctrineMigrationTestCase
 *
 * WebTestCase with Doctrine Migrations setup before each test is run.
 *
 * NOTE: This WebTestCase WILL DROP the DB on each test run. Please run this only against a dev DB
 *
 * Each test will have a clean migrated DB, useful for testing in a production like environment.
 *
 * @package Malwarebytes\TestBundle\Test
 */
class DoctrineMigrationTestCase extends BaseWebTestCase {
    /** @var  EntityManager */
    protected $em;

    /** @var  MigrationTestCaseDriver */
    protected $driver;



    public function setUp()
    {
        parent::setUp();


        $this->em=$this->client->getContainer()->get('doctrine')->getManager();

        $config=$this->client->getContainer()->getParameter('malwarebytes_test.config');
        $driver = '\\Malwarebytes\\TestBundle\\Drivers\\MigrationTestCase\\'.$config['doctrine_migration_test_driver'];
        if (!class_exists($driver)) {
            throw new \Exception("DoctrineMigrationTestCase Driver '$driver' does not exist. There is a configuration problem in testbundle:doctrine_migration_test_driver.");
        }
        $this->driver = new $driver();
        if (!is_subclass_of($this->driver,"\\Malwarebytes\\TestBundle\\Drivers\\MigrationTestCase\\MigrationTestCaseDriver")) {
            throw new \Exception("$driver does not properly implement '\\Malwarebytes\\TestBundle\\Drivers\\MigrationTestCase\\MigrationTestCaseDriver'. Please correct this.");
        }

        if ($this->driver->setUp($this->client) === true) {
            $event = $this->client->getContainer()->get('malwarebytes_test.post_schema_setup_event');
            $dispatcher = $this->client->getContainer()->get('event_dispatcher');
            $dispatcher->dispatch('malwarebytes_test.events.post_schema_setup', $event);
        }
    }

    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        // The constructor has to maintain compatibility with phpunit constructor or @dataProvider will fail to work.

        if (is_a($name,'Symfony\Bundle\FrameworkBundle\Client')) {
            $this->client = $name;
        } else {
            parent::__construct($name, $data, $dataName);
        }
    }

    public function tearDown()
    {
        if (isset($this->driver) && is_subclass_of($this->driver,"\\Malwarebytes\\TestBundle\\Drivers\\MigrationTestCase\\MigrationTestCaseDriver")) {
            $this->driver->tearDown($this->client);
        }
    }
}