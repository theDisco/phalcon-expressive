<?php

namespace PhalconExpressiveTest;

use Phalcon\Mvc\Router;
use Phalcon\DI\FactoryDefault;
use PhalconExpressive\PhalconRouter;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Diactoros\ServerRequest as Request;
use Zend\Diactoros\Uri;
use Zend\Expressive\Router\Route;

class PhalconRouterTest extends TestCase
{
    /**
     * @group unit
     */
    public function testAddRouteToTheRouter()
    {
        $phalcon = self::createPhalconRouter();
        $router = new PhalconRouter($phalcon);
        $router->addRoute(self::createTestRoute());
        $actual = $phalcon->getRouteByName('testRoute');

        $this->assertEquals($actual->getPattern(), '/test');
        $this->assertEquals($actual->getPaths()['namespace'], 'Test\Middleware');
        $this->assertEquals($actual->getHttpMethods(), ['GET']);
        $this->assertEquals($actual->getName(), 'testRoute');
    }

    /**
     * @group unit
     * @dataProvider matchRouteDataProvider
     * @param string $method
     * @param bool $success
     */
    public function testMatchRoute($method, $success)
    {
        $_SERVER['REQUEST_METHOD'] = $method;

        $router = new PhalconRouter;
        $router->addRoute(self::createTestRoute());
        $match = $router->match($this->createRequestMock());

        if ($success) {
            $this->assertTrue($match->isSuccess());
        }

        if (!$success) {
            $this->assertTrue($match->isFailure());
        }
    }

    /**
     * @return array
     */
    public function matchRouteDataProvider()
    {
        return [
            ['GET', true],
            ['POST', false],
        ];
    }

    /**
     * @group unit
     */
    public function testAssignParamsForRouteWithPlaceholders()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $router = new PhalconRouter;
        $router->addRoute(self::createTestRoute('/{routeParam}'));
        $match = $router->match($this->createRequestMock());

        $this->assertEquals(['routeParam' => 'test'], $match->getMatchedParams());
    }

    /**
     * @group unit
     */
    public function testGenerateUriFromRouteName()
    {
        $router = new PhalconRouter;
        $router->addRoute(self::createTestRoute('/{routeParam}'));
        $expected = $router->generateUri('testRoute', ['routeParam' => 'just-a-test']);

        $this->assertEquals($expected, '/just-a-test');
    }

    /**
     * @return Router
     */
    private static function createPhalconRouter()
    {
        $router = new Router;
        $router->clear();
        $router->setDi(new FactoryDefault);

        return $router;
    }

    /**
     * @param string $path
     * @return Route
     */
    private static function createTestRoute($path = '/test')
    {
        return new Route($path, 'Test\Middleware', ['GET'], 'testRoute');
    }

    /**
     * @return Request
     */
    private function createRequestMock()
    {
        $uri = $this->getMock(Uri::class, ['getPath']);
        $uri->expects($this->once())
            ->method('getPath')
            ->will($this->returnValue('/test'));

        $request = $this->getMock(Request::class, ['getUri']);
        $request->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue($uri));

        return $request;
    }
}
