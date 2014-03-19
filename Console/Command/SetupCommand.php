<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 3/7/14
 * Time: 1:07 PM
 */

namespace Malwarebytes\TestBundle\Console\Command;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\DialogHelper;
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
        self::init();

        /** @var DialogHelper $dialog */
        $dialog = $this->getHelperSet()->get('dialog');

        if (!$dialog->askConfirmation($output,'<question>Do you want to setup your project to use Malwarebytes Test Bundle? [y|N] </question>',false)) {
            return;
        }

        if ( !self::isParametersYmlDistConfigured() ) {
            $output->writeln("<info>Updating parameters.yml.dist...</info>");
            $this->setupParametersYmlDist();
            $output->writeln("<info>Updated parameters.yml.dist!</info>");
        } else {
            $output->writeln("<info>File 'parameters.yml.dist' already setup, not modifying file.</info>");
        }

        if ( !self::isConfigTestYmlConfigured() ) {
            $output->writeln("<info>Updating config_test.yml...</info>");
            $this->setupConfigYml();
            $output->writeln("<info>Updated config_test.yml!</info>");
        } else {
            $output->writeln("<info>File 'config_test.yml' already setup, not modifying file.</info>");
        }

        if ( !self::isPhpUnitConfigured() ) {
            $output->writeln("<info>Updating phpunit.xml.dist...</info>");
            $this->setupPhpunitXmlDist($output);
            $output->writeln("<info>Updated phpunit.xml.dist!</info>");
        } else {
            $output->writeln("<info>File 'phpunit.xml.dist' already setup, not modifying file.</info>");
        }

        if ( !self::isParametersYmlConfigured() ) {
            $output->writeln("");
            $output->writeln("Please run composer.phar install to set test_db_* parameters in parameters.yml");
            $output->writeln("and finish setup.");
            //$output->write("<info>Regenerating parameters.yml via composer install...</info>");
            //Script
        }

        $output->writeln("");
        $output->writeln("<info>Malwarebytes TestBundle setup complete!</info>");

    }

    private function setupPhpunitXmlDist($output)
    {
        $phpunitConfigPath = self::$rootDir . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'phpunit.xml.dist';
        $samplePhpUnitConfigPath = realpath(__DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "Resources" . DIRECTORY_SEPARATOR . "doc" . DIRECTORY_SEPARATOR . "ConfigExamples" . DIRECTORY_SEPARATOR . "phpunit.xml.dist");

        if ( file_exists($phpunitConfigPath) ) {
            $incrementor = 0;
            while( file_exists($phpunitConfigPath . ".old." . $incrementor) ) {
                $incrementor++;
            }
            $output->writeln("<info>Found existing phpunit.xml.dist, moving to ".$phpunitConfigPath . ".old." . $incrementor."...</info>");
            rename($phpunitConfigPath,$phpunitConfigPath . ".old." . $incrementor);
        }

        copy($samplePhpUnitConfigPath,$phpunitConfigPath);
    }

    private function setupConfigYml()
    {
        $testSetupParameters = <<<'EOD'


# MalwarebytesTestBundle Generated Code Below
doctrine:
    dbal:
        driver:   %test_db_driver%
        host:     %test_db_host%
        port:     %test_db_port%
        dbname:   %test_db_name%
        user:     %test_db_user%
        password: %test_db_password%
        path:     %test_db_path%
        charset:  UTF8
# End of MalwarbytesTestBundle Generated Code
EOD;
        file_put_contents(self::$configDir . DIRECTORY_SEPARATOR . "config_test.yml",$testSetupParameters,FILE_APPEND);
    }

    private function setupParametersYmlDist()
    {
        $testSetupParameters = <<<'EOD'


    # MalwarebytesTestBundle Generated Code Below
    # used for functional / integration tests
    test_db_driver:    pdo_sqlite
    test_db_host:
    test_db_port:
    test_db_name:
    test_db_user:
    test_db_password:
    test_db_path:      %kernel.root_dir%/cache/test/data.sqlite
    # End of MalwarebytesTestBundle Generated Code
EOD;
        file_put_contents(self::$configDir . DIRECTORY_SEPARATOR . "parameters.yml.dist",$testSetupParameters,FILE_APPEND);
    }

    /**
     * Checks if Malwarebytes TestBundle has been properly configured.
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
        return self::isParamsYmlConfigured(self::$configDir.DIRECTORY_SEPARATOR."parameters.yml");
    }

    private static function isParametersYmlDistConfigured()
    {
        return self::isParamsYmlConfigured(self::$configDir.DIRECTORY_SEPARATOR."parameters.yml.dist");
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
