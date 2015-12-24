# Phalcon 2 integration for Zend Expressive

This project is supposed to provide a bridge between Zend Expressive and Phalcon Framework.

# Installation

This step is optional but suggested to kick start your new project. Use Zend Expressive skeleton 
by running the command below, to create a suggested project structure.

```
$ composer create-project zendframework/zend-expressive-skeleton <project-path>
```

When asked to select the router, type:

```
aferalabs/phalcon-expressive:dev-master
```

# Phalcon Router

Creation of a router boils down to this simple lines:

```php
use PhalconExpressive\PhalconRouter;

$router = new PhalconRouter;
```

`PhalconExpressive\PhalconRouter` depends on `Phalcon\Mvc\Router` and `Phalcon\Mvc\Url`. If you want to provide 
alternative instances of these services, you might do so by passing them as constructor arguments. Otherwise 
they will be created using default values.

```php
use Phalcon\Mvc;
use PhalconExpressive\PhalconRouter;
use Zend\Expressive\AppFactory;

$url = new Mvc\Url;
$url->setBaseUri('/blog');

$router = new Mvc\Router;
$router->setEventsManager(new Phalcon\Events\Manager);

$router = new PhalconRouter(null, $url);
$app = AppFactory::create(null, $router);
```

The simplest way to integrate the Phalcon router is to define it in invokable dependencies in the routes
config:

```php
return [
    'dependencies' => [
        'invokables' => [
            Zend\Expressive\Router\RouterInterface::class => PhalconExpressive\PhalconRouter::class,
        ],
    ],
    'routes' => [
        [
            'name' => 'home',
            'path' => '/',
            'middleware' => App\Action\HomePageAction::class,
            'allowed_methods' => ['GET'],
        ],
        [
            'name' => 'api.ping',
            'path' => '/api/ping',
            'middleware' => App\Action\PingAction::class,
            'allowed_methods' => ['GET'],
        ],
    ],
];
```

# TODO

* Finish documentation
* Extend router functionality
* Add DI integration
* Add volt integration
