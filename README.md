# Phalcon 2 integration for Zend Expressive

This project is supposed to provide a bridge between Zend Expressive and Phalcon Framework.
It is partially tested but it might still include a lot of bugs, therefore do not consider it
as production ready.

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

When asked to select DI container, type:

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

# Phalcon DI

Phalcon Expressive includes an implementation of `Interop\Container\ContainerInterface` that is
build on top of `Phalcon\DI`. In order to set up the container, create a new file `config/container.php`
and add following content to it:

```php
<?php

use PhalconExpressive\PhalconDI;

// Load configuration
$config = require 'config.php';

$di = new PhalconDI;
$di->set('config', $config);

// Inject factories
foreach ($config['dependencies']['factories'] as $name => $object) {
    $di->set($name, function() use ($object, $di) {
        return (new $object)->__invoke($di);
    });
}

// Inject invokables
foreach ($config['dependencies']['invokables'] as $name => $object) {
    $di->set($name, $object);
}

return $di;
```

That's it, `Phalcon\DI` should be set up and ready to serve as DI container for Zend Expressive.

# TODO

* Extend router functionality
* Add volt integration
