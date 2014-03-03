<?php
/**
 * Created by PhpStorm.
 * User: jonathan
 * Date: 2/6/14
 * Time: 12:49 AM
 */

namespace Malwarebytes\TestBundle\Test;

use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BaseWebTestCase extends WebTestCase
{
    /** @var  Client */
    protected $client;

    public function setUp()
    {
        parent::setUp();

        $this->client = self::createClient();
    }

    protected function assertRedirect($location)
    {
        $response = $this->client->getResponse();
        self::assertTrue($response->isRedirect(), 'Response is not a redirect, got status code: '.$response->getStatusCode());
        self::assertEquals('http://localhost'.$location, $response->headers->get('Location'));

    }

    protected function assertRedirectByRoute($route)
    {
        $response = $this->client->getResponse();
        self::assertTrue($response->isRedirect(), 'Response is not a redirect, got status code: '.$response->getStatusCode());
        self::assertEquals('http://localhost'.$this->client->getContainer()->get('router')->generate($route), $response->headers->get('Location'));

    }
}
