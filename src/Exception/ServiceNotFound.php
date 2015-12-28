<?php

namespace PhalconExpressive\Exception;

use Exception;
use Interop\Container\Exception\NotFoundException;

/**
 * Class ServiceNotFound
 * @package PhalconExpressive\Exception
 */
class ServiceNotFound extends Exception implements NotFoundException
{
}
