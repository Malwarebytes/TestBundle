# Test Cases

## DoctrineFixturesTestCase

DoctrineFixturesTestCase is used to set up test data fixtures in the database to test against. Before each test, the database is cleared and the test fixtures are reset. This behavior may be changed by setting ```$this->clearDB=false;```. To use the DoctrineFixturesTestCase, just extend the class and implement the getTestFixturePath() function. The function should return the path where the test fixtures reside, such as ```@MalwarebytesTestBundle\DataFixtures\Test\TestService```. Within the TestService folder, all classes that implement ```\Doctrine\Common\DataFixtures\FixtureInterface``` will be executed and loaded into the DB. Please refer to DataFixtures documentation on how to load DataFixtures data.

## DoctrineMigrationTestCase

DoctrineMigrationTestCase is used to set up a base production environment via doctrine migrations. Before each test is run, the database is cleared and the migration files are run. This is equivalent to running ``` $ app/console doctrine:database:drop ; app/console doctrine:migrations:migrate``` before each test is executed. As long as you have properly set up migrations, extending DoctrineMigrationTestCase will give you this behavior.
