# Phalcon 2 integration for Zend Expressive

This project is supposed to provide a bridge between Zend Expressive and Phalcon Framework.

# Installation

This step is optional but suggested to kick start your new project.

# Phalcon Router

Creation of a router boils down to this simple lines:

```php
use PhalconExpressive\PhalconRouter;

$router = new PhalconRouter;
```

`PhalconRouter` depends on `Phalcon\Mvc\Router` and `Phalcon\Mvc\Url`. If you want to provide alternative
instances of these services, you might do so by passing them as constructor arguments. Otherwise they will
be created using default values.

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

# TODO

* Finish documentation
* Extend router functionality
* Add DI integration
* Add volt integration
