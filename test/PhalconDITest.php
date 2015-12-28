<?php

namespace PhalconExpressiveTest;

use PhalconExpressive\PhalconDI;
use PHPUnit_Framework_TestCase as TestCase;

class PhalconDITest extends TestCase
{
    public function testValidateIfServiceExistsInTheContainer()
    {
        $di = new PhalconDI;
        $this->assertFalse($di->has('SomeService'));
        $di->set('SomeService', '\stdClass');
        $this->assertTrue($di->has('SomeService'));
    }

    public function testThrowNotFoundExceptionIfServiceCannotBeLocated()
    {
        $this->setExpectedException('\PhalconExpressive\Exception\ServiceNotFound');
        $di = new PhalconDI;
        $di->get('NotExistent');
    }

    public function testThrowContainerExceptionIfAnErrorOccurred()
    {
        $this->setExpectedException('\PhalconExpressive\Exception\ContainerError');
        $di = new PhalconDI;
        $di->set('SomeService', 'Klass\Does\Not\Exist');
        $di->get('SomeService');
    }
}
