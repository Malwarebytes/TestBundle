<?php
/**
 * User: Jonathan Chan <jchan@malwarebytes.org>
 * Date: 11/5/13
 * Time: 1:32 PM
 */


namespace Malwarebytes\TestBundle\Test;


use Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand;
use Doctrine\ORM\EntityManager;
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
class DoctrineMigrationTestCase extends WebTestCase {

    /** @var  App */
    protected $application;
    /** @var  Client */
    protected $client;
    /** @var  EntityManager */
    protected $em;

    public function setUp()
    {
        $this->client = self::createClient();

        $this->em = self::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->dropDatabase();

        $this->application = new App(self::$kernel);
        $this->application->add(new MigrateCommand());
        $this->application->setAutoExit(false);

        $input = new ArrayInput(array('command' => 'doctrine:migrations:migrate', '-q' => true, '-n' => true));
        $this->application->run($input, null);
    }

    public function __construct(Client $client = null)
    {
        $this->client = $client;
    }
}