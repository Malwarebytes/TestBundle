# Testing Methodologies


There are three different types of tests we recommend utilizing to thoroughly test our code. These tests are the unit test, the functional test, and the integration test. This documentation is by no means complete instructions on the purposes of these tests, but it should give you a basic understanding, especially if you are familiar with testing.

## Unit Tests

Unit tests should be utilized to test the smallest atomic unit of code, usually a single function. In a single function, all variables should be isolated and a test should be created to enter every single logical code path. If the class relies on a complex variable, such as another service or a function call from another class or if the response is returned via service calls, we utilize PHPUnit Mocking class.

[PHP Mocking Tutorial](http://www.jmccc.com/blog/archives/2013/01/16/phpunit-mocking/)

## Functional Tests

Functional tests are used to tests the interaction of different classes together. While arguably it can be considered an integration test, we utilize functional tests with the database server. Our rationale to using the database is to fully test the ORM layer instead of mocking that out to catch any irregularities we may encounter with Doctrine. Usually, for functional tests, we test at the service or controller layer. For isolating the tested service or controller from other dependent services, we utilize mocked services. For database-driven inputs, we utilize DoctrineFixtureTestCases to generate test scenarios with test fixtures. Please refer to the documentation on the different TestCases.

```php

$service = new Service();
$mockDependentService = $this->getMock('Acme\DependentBundle\DependentService', ...);
$mockDependentService->expects($this->once())
    ->method('usedMethod')
    ->will($this->returnValue("expected result");
$service->setDependentService($mockDependentService);
$service->process();
// Assert Results
```

## Integration Tests
Our last tests we utilize are integration tests. They are full fledged end-to-end tests that utilize the real system as much as possible. Instead of mocking out external library services, we utilize real servers. We avoid setting up both special DB Test Fixtures or any mocks utilizing the Symfony2 testing $client->request() functionality testing end to end as much as possible. For projects utilizing the migration files, we have DoctrineMigrationTestCase to reset the database to a clean migrated DB before each test. For bugs reported by QA, we write regression tests to repeat the scenarios presented by QA. Instead of manually manipulating the data, if possible, we create libraries to call production code endpoints to set the database in the correct states to test for functionality.


