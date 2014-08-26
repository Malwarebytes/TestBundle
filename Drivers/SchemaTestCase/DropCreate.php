<?php
/**
 * Created by PhpStorm.
 * User: jchan
 * Date: 8/25/14
 * Time: 11:20 PM
 */

namespace Malwarebytes\TestBundle\Drivers\SchemaTestCase;


use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;

class DropCreate implements SchemaTestCaseDriver {

    /**
     * Function called to setup database and/or environment for testing
     *
     * @param Client $client
     * @return null
     */
    public function setUp(Client $client)
    {
        $dropDatabaseDoctrineCommand = new DropDatabaseDoctrineCommand();
        $dropDatabaseDoctrineCommand->setContainer($client->getContainer());
        $dropDBInput = new ArrayInput(array('--force' => true));
        $output = new NullOutput();
        $dropDatabaseDoctrineCommand->run($dropDBInput,$output);

        $createDatabaseDoctrineCommand = new CreateDatabaseDoctrineCommand();
        $createDatabaseDoctrineCommand->setContainer($client->getContainer());
        $createDBInput = new ArrayInput(array());
        $createDatabaseDoctrineCommand->run($createDBInput,$output);



        $connection = $client->getContainer()->get('doctrine')->getConnection();
        $params = $connection->getParams();

        $name = isset($params['path']) ? $params['path'] : (isset($params['dbname']) ? $params['dbname'] : false);

        $connection->getSchemaManager()->dropDatabase($name);

        $em = $client->getContainer()->get('doctrine')->getManager();
        $tool = new SchemaTool($em);
        $metadatas = $em->getMetadataFactory()->getAllMetadata();


        $tool->createSchema($metadatas);
    }

    /**
     * Function called to teardown database and/or reset the environment
     *
     * @param Client $client
     * @return null
     */
    public function tearDown(Client $client)
    {
    }
}