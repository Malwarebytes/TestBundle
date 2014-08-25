<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 3/25/14
 * Time: 11:16 AM
 */

namespace Malwarebytes\TestBundle\Drivers;

use Symfony\Bundle\FrameworkBundle\Client;

interface TestCaseDriver {
    /**
     * Function called to setup database and/or environment for testing
     *
     * True return value will trigger a PostSchemaSetup event. False suppresses the event.
     *
     * @param Client $client
     * @return boolean
     */
    public function setUp(Client $client);

    /**
     * Function called to teardown database and/or reset the environment
     *
     * @param Client $client
     * @return null
     */
    public function tearDown(Client $client);
} 