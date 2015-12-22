<?php

namespace PhalconExpressive;

use Phalcon\Mvc\Router;
use Phalcon\Mvc\Url;
use Phalcon\DI as PhalconDI;
use Phalcon\Http\Request as PhalconRequest;
use Psr\Http\Message\ServerRequestInterface as Request;
use Zend\Expressive\Router\Exception;
use Zend\Expressive\Router\Route;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Router\RouteResult;

/**
 * Class PhalconRouter
 * @package PhalconExpressive
 */
final class PhalconRouter implements RouterInterface
{
    /**
     * @var Router
     */
    private $router;

    /**
     * @var Url
     */
    private $url;

    /**
     * @param Router|null $router
     * @param Url|null $url
     */
    public function __construct(Router $router = null, Url $url = null)
    {
        $this->router = $router;
        $this->url = $url;
        $di = new PhalconDI;

        if (!$router) {
            $di->setShared('request', new PhalconRequest);
            $this->router = new Router;
            $this->router->clear();
            $this->router->setDI($di);
            $di->setShared('router', $this->router);
        }

        if (!$url) {
            $this->url = new Url;
            $this->url->setBaseUri('/');
            $this->url->setDI($di);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addRoute(Route $route)
    {
        // TODO allow named parameters through options.
        // TODO allow usage of :controller, :action, etc.

        if ($this->router->wasMatched()) {
            throw new Exception\RuntimeException('Route was already matched.');
        }

        // Necessary for phalcon not to alter the original middleware.
        $middleware = $route->getMiddleware() . '\MockController::mockAction';

        $r = $this->router->add($route->getPath(), $middleware, $route->getAllowedMethods());
        $r->setName($route->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function match(Request $request)
    {
        $this->router->handle($request->getUri()->getPath());

        if (!$this->router->wasMatched()) {
            // TODO Is it worth to validate, if route matches but the method is incompatible?
            return RouteResult::fromRouteFailure();
        }

        $matchedRoute = $this->router->getMatchedRoute();

        return RouteResult::fromRouteMatch(
            $matchedRoute->getName(),
            $this->router->getNamespaceName(),
            $this->collectParams($matchedRoute)
        );
    }

    /**
     * @param Router\Route $route
     * @return array
     */
    private function collectParams(Router\Route $route)
    {
        $matches = $this->router->getMatches();
        $params  = [];

        foreach ($route->getPaths() as $name => $position) {
            if (isset($matches[$position])) {
                $params[$name] = $matches[$position];
            }
        }

        return $params;
    }

    /**
     * {@inheritdoc}
     */
    public function generateUri($name, array $substitutions = [])
    {
        return $this->url->get(array_merge(['for' => $name], $substitutions));
    }
}