<?php

namespace Core;

use Interop\Container\ContainerInterface;

class Controller
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
}