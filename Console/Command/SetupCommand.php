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
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Parser;

class SetupCommand extends Command {
    public static $rootDir;
    public static $configDir;

    private static function init()
    {
        self::$rootDir = realpath(__DIR__ . DIRECTORY_SEPARATOR . ".."
            . DIRECTORY_SEPARATOR . ".."
            . DIRECTORY_SEPARATOR . ".."
            . DIRECTORY_SEPARATOR . ".."
            . DIRECTORY_SEPARATOR . ".."
            . DIRECTORY_SEPARATOR . ".."
            . DIRECTORY_SEPARATOR . "..");
        self::$configDir = self::$rootDir
            . DIRECTORY_SEPARATOR . "app"
            . DIRECTORY_SEPARATOR . "config";
    }


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
        self::init();
        self::isConfigTestYmlConfigured();
        if (
            self::isParametersYmlConfigured() &&
            self::isParametersYmlDistConfigured() &&
            self::isConfigTestYmlConfigured() &&
            self::isPhpUnitConfigured()
        ) {
            return true;
        } else {
            return false;
        }
    }


    private static function isParametersYmlConfigured()
    {
        self::isParamsYmlConfigured(self::$configDir.DIRECTORY_SEPARATOR."parameters.yml");
    }

    private static function isParametersYmlDistConfigured()
    {
        self::isParamsYmlConfigured(self::$configDir.DIRECTORY_SEPARATOR."parameters.yml.dist");
    }

    private static function isParamsYmlConfigured($parametersYmlFile)
    {
        $yaml = new Parser();


        try {
            $value = $yaml->parse(file_get_contents($parametersYmlFile));
        } catch (ParseException $e) {
            printf("Unable to parse the app/config/parameters.yml string: %s", $e->getMessage());
        }

        if (
            array_key_exists('test_db_driver',$value['parameters']) &&
            array_key_exists('test_db_host',$value['parameters']) &&
            array_key_exists('test_db_port',$value['parameters']) &&
            array_key_exists('test_db_name',$value['parameters']) &&
            array_key_exists('test_db_user',$value['parameters']) &&
            array_key_exists('test_db_password',$value['parameters']) &&
            array_key_exists('test_db_path',$value['parameters'])
        ) {
            return true;
        } else {
            return false;
        }
    }

    private static function isConfigTestYmlConfigured()
    {
        $file = self::$configDir . DIRECTORY_SEPARATOR . "config_test.yml";
        $yaml = new Parser();


        try {
            $value = $yaml->parse(file_get_contents($file));
        } catch (ParseException $e) {
            printf("Unable to parse the app/config/config_test.yml string: %s", $e->getMessage());
        }

        if (
            array_key_exists('doctrine',$value) &&
            array_key_exists('dbal',$value['doctrine']) &&
            array_key_exists('driver',$value['doctrine']['dbal']) &&
            array_key_exists('host',$value['doctrine']['dbal']) &&
            array_key_exists('port',$value['doctrine']['dbal']) &&
            array_key_exists('dbname',$value['doctrine']['dbal']) &&
            array_key_exists('user',$value['doctrine']['dbal']) &&
            array_key_exists('password',$value['doctrine']['dbal']) &&
            array_key_exists('path',$value['doctrine']['dbal']) &&
            $value['doctrine']['dbal']['driver'] == "%test_db_driver%" &&
            $value['doctrine']['dbal']['host'] == "%test_db_host%" &&
            $value['doctrine']['dbal']['port'] == "%test_db_port%" &&
            $value['doctrine']['dbal']['dbname'] == "%test_db_name%" &&
            $value['doctrine']['dbal']['user'] == "%test_db_user%" &&
            $value['doctrine']['dbal']['password'] == "%test_db_password%" &&
            $value['doctrine']['dbal']['path'] == "%test_db_path%"
        ) {
            return true;
        } else {
            return false;
        }
    }


    private static function isPhpUnitConfigured()
    {
        $isFunctionalTestSuiteConfigured = false;
        $isUnitTestSuiteConfigured = false;
        $isIntegrationTestSuiteConfigured = false;
        $phpunitXmlDistFile = self::$rootDir . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "phpunit.xml.dist";

        $xml = file_get_contents($phpunitXmlDistFile);


        $crawler = new Crawler($xml);
        $test = $crawler->filterXPath("//phpunit/testsuites");

        /** @var $domElement \DOMElement */
        foreach ($test->children() as $domElement) {
            switch ($domElement->getAttribute('name')) {
                case "UnitTests":
                    $isUnitTestSuiteConfigured = true;
                    break;
                case "FunctionalTests":
                    $isFunctionalTestSuiteConfigured = true;
                    break;
                case "IntegrationTests":
                    $isIntegrationTestSuiteConfigured = true;
                    break;
            }
        }

        if (
            $isUnitTestSuiteConfigured &&
            $isFunctionalTestSuiteConfigured &&
            $isIntegrationTestSuiteConfigured
        ) {
            return true;
        } else {
            return false;
        }
    }
}
