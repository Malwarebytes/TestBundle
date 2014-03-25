<?php

namespace Malwarebytes\TestBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\DependencyInjection\Container;

class ImportDataEvent extends Event
{
    /** @var Container */
    private $container;

    public function setContainer($container)
    {
        $this->container = $container;
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function myTestFunction()
    {

    }
}
