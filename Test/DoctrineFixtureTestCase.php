<?php
/**
 * User: Jonathan Chan <jchan@malwarebytes.org>
 * Date: 12/18/12
 * Time: 8:46 PM
 */

namespace Malwarebytes\TestBundle\Test;


use Doctrine\ORM\EntityManager;

/**
 * Note: Be careful to consistently use $this->em or your own self::createClient()->getEm(); if you mix this up, doctrine
 * will have very inconsistent behavior.
 *
 * Class DoctrineFixtureTestCase
 * @package Malwarebytes\Licensing\MigrationBundle\Test\fixtures\doctrine
 */
abstract class DoctrineFixtureTestCase extends BaseWebTestCase
{
    /** @var EntityManager */
    protected $em;
    protected $container;
    protected $clearDB=true;

    public function setUp()
    {
        $classLoader = new \Doctrine\Common\ClassLoader('Malwarebytes\Test', __DIR__.DIRECTORY_SEPARATOR."fixtures".DIRECTORY_SEPARATOR.'doctrine');
        $classLoader->register();


        $this->client=static::createClient();
        $this->container=$this->client->getContainer();
        $this->em=$this->client->getContainer()->get('doctrine')->getManager();
        $this->em->getConfiguration()->getMetadataDriverImpl()->addDriver($this->em->getConfiguration()->newDefaultAnnotationDriver(array(__DIR__.DIRECTORY_SEPARATOR."fixtures".DIRECTORY_SEPARATOR.'doctrine'.DIRECTORY_SEPARATOR."Malwarebytes".DIRECTORY_SEPARATOR."Test"), false),"Malwarebytes\Test");
        $this->em->getConfiguration()->addEntityNamespace('TestSpace', 'Malwarebytes\Test');

        // set $this->clearDB = false if you want persisted DB between tests
        if($this->clearDB===false) {
            $fixtureMonitor=null;
        } else {
            $this->loadFixtures();
            return;
        }

        try
        {
            $fixtureMonitor=$this->em->find('TestSpace:FixtureMonitor',$this->getTestFixturePath());
        }
        catch(\Doctrine\DBAL\DBALException $e)
        {
            if(strstr($e->getMessage(), "no such table") || strstr($e->getMessage(),"doesn't exist")) {
                echo "Schema/DB does not exist - attempting to create db schema.\n";
                $this->loadFixtures();
                return;
            } else {
                throw $e;
            }
        }

        $kernel=$this->container->get("kernel");
        $testFixturePath=$kernel->locateResource($this->getTestFixturePath());
        $currentMD5Hash=$this->MD5_DIR($testFixturePath);
        if(is_null($fixtureMonitor)) {
            $this->loadFixtures();
        } else if ($fixtureMonitor->getHash()!=$currentMD5Hash) {
            echo "Test fixtures has changed since last run, reloading test fixtures...\n";
            $this->loadFixtures();
        }
    }

    protected function loadFixtures()
    {
        $kernel=$this->client->getContainer()->get("kernel");
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $metadatas = $this->em->getMetadataFactory()->getAllMetadata();
        $testFixturePath=$kernel->locateResource($this->getTestFixturePath());
        $tool->dropSchema($metadatas);
        $tool->createSchema($metadatas);
        //$tool->dropSchema(array($this->em->getClassMetadata('Malwarebytes\Test\FixtureMonitor')));
        //$tool->createSchema(array($this->em->getClassMetadata('Malwarebytes\Test\FixtureMonitor')));

        // Get the tag
        $currentMD5Hash=$this->MD5_DIR($testFixturePath);
        $fixtureMonitor=new \Malwarebytes\Test\FixtureMonitor();
        $fixtureMonitor->setPath($this->getTestFixturePath());
        $fixtureMonitor->setHash($currentMD5Hash);



        /* We do not run from command line anymore so that we can use the same entity manager */
        //$this->_application = new \Symfony\Bundle\FrameworkBundle\Console\Application($kernel);
        //$this->_application->setAutoExit(false);
        //$this->runConsole("doctrine:schema:drop", array("--force" => true));
        //$this->runConsole("doctrine:schema:create", array("--em" =>$this->em));




        $loader = new \Doctrine\Common\DataFixtures\Loader();
        $loader->loadFromDirectory($testFixturePath);
        $purger = new \Doctrine\Common\DataFixtures\Purger\ORMPurger();
        $executor = new \Doctrine\Common\DataFixtures\Executor\ORMExecutor($this->em, $purger);
        $executor->execute($loader->getFixtures());

        $this->em->persist($fixtureMonitor);
        $this->em->flush();
        //$this->runConsole("doctrine:fixtures:load", array("--fixtures" => $testFixturePath));
    }

    /**
     * override this function to specify location for test fixtures.
     *
     * The string is passed to $kernel->locateResource()
     *
     * @return string
     */
    protected abstract function getTestFixturePath();

/*    protected function runConsole($command, Array $options = array())
    {
        $options["-e"] = "test";
        $options["-q"] = null;
        $options = array_merge($options, array('command' => $command));
        return $this->_application->run(new \Symfony\Component\Console\Input\ArrayInput($options));
    }*/

    protected function MD5_DIR($dir)
    {
        if (!is_dir($dir))
        {
            return false;
        }

        $filemd5s = array();
        $d = dir($dir);

        while (false !== ($entry = $d->read()))
        {
            if ($entry != '.' && $entry != '..')
            {
                if (is_dir($dir.'/'.$entry))
                {
                    $filemd5s[] = $this->MD5_DIR($dir.'/'.$entry);
                }
                else
                {
                    $filemd5s[] = md5_file($dir.'/'.$entry);
                }
            }
        }
        $d->close();
        return md5(implode('', $filemd5s));
    }

    protected function tearDown()
    {
        $refl = new \ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }
    }
}

