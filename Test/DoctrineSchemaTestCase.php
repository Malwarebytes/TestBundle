<?php
/**
 * Created by PhpStorm.
 * User: jchan
 * Date: 8/25/14
 * Time: 4:25 PM
 */

namespace Malwarebytes\TestBundle\Test;


use Doctrine\ORM\EntityManager;
use Malwarebytes\TestBundle\Drivers\SchemaTestCase\SchemaTestCaseDriver;
use Symfony\Bundle\FrameworkBundle\Console\Application as App;

class DoctrineSchemaTestCase extends BaseWebTestCase {
    /** @var  App */
    protected $application;
    /** @var  EntityManager */
    protected $em;
    /** @var  SchemaTestCaseDriver */
    protected $driver;

    public function setUp()
    {
        parent::setUp();

        $config=$this->client->getContainer()->getParameter('malwarebytes_test.config');
        $driver = '\\Malwarebytes\\TestBundle\\Drivers\\SchemaTestCase\\'.$config['doctrine_schema_test_driver'];
        if (!class_exists($driver)) {
            throw new \Exception("DoctrineSchemaTestCase Driver '$driver' does not exist. There is a configuration problem in testbundle:doctrine_schema_test_driver.");
        }
        $this->driver = new $driver();
        if (!is_subclass_of($this->driver,"\\Malwarebytes\\TestBundle\\Drivers\\SchemaTestCase\\SchemaTestCaseDriver")) {
            throw new \Exception("$driver does not properly implement '\\Malwarebytes\\TestBundle\\Drivers\\SchemaTestCase\\SchemaTestCaseDriver'. Please correct this.");
        }

        $this->driver->setUp($this->client);
    }

    public function __construct(Client $client = null)
    {
        $this->client = $client;
    }

    public function tearDown()
    {
        if (isset($this->driver) && is_subclass_of($this->driver,"\\Malwarebytes\\TestBundle\\Drivers\\SchemaTestCase\\TestCaseDriver")) {
            $this->driver->tearDown($this->client);
        }
    }
} 