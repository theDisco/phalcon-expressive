<?php

namespace PhalconExpressive;

use Interop\Container\ContainerInterface;
use Phalcon\DI;
use PhalconExpressive\Exception;

/**
 * Class PhalconDI
 * @package PhalconExpressive
 */
class PhalconDI implements ContainerInterface
{
    /**
     * @var DI
     */
    private $di;

    public function __construct()
    {
        $this->di = new DI;
    }

    /**
     * {@inheritdoc}
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new Exception\ServiceNotFound("Service `$id` not found in the container.");
        }

        try {
            return $this->di->getShared($id);
        } catch (DI\Exception $e) {
            throw new Exception\ContainerError("Cannot retrieve `$id` from the container.", 0, $e);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function has($id)
    {
        return $this->di->has($id);
    }

    /**
     * @param string $id
     * @param string|callable|object $val
     * @return void
     */
    public function set($id, $val)
    {
        $this->di->attempt($id, $val, true);
    }
}
