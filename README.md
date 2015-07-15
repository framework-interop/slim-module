Slim module for framework-interop
=================================

This package contains a *framework-interop* module that provides a Slim application.

It can be used by other framework-agnostic modules to ensure that a Slim application
is present and can be used.

##Framework-agnostic modules?

Have a look at [the demo](http://github.com/framework-interop/framework-interop-demo).
It shows 3 modules, one using Symfony 2, one using Silex and one using Zend Framework 1.

##How it works

Let's assume you are writing a framework-agnostic module using *framework-interop*. Your module might
offer web pages to the user. For this, it must be able to catch HTTP requests and respond accordingly.
Using *framework-interop*, you can do this by implementing a [HttpModuleInterface](https://github.com/framework-interop/http-module-interface).

However, this can be time consuming to directly implement a middleware. Most of the time, it is easier to
use an existing MVC framework, like [Slim](http://www.slimframework.com/).

- The `SlimFrameworkModule` is a module that will load and initialize Slim for you.
- Then, all you have to do is extend the `AbstractSlimModule` class to add routes to the Slim application.

##An example

Here is a minimalistic sample:

```php
namespace Acme\FrontendModule;

use Interop\Framework\Slim\AbstractSlimModule;
use Symfony\Component\HttpFoundation\Response;

/**
 * The frontend module is a Slim application.
 */
class SampleModule extends AbstractSlimModule
{

    public function getName()
    {
        return 'sample';
    }

	public function init()
	{
	    // Let's get the slim application
		$app = $this->getSlimApp();

		// Let's add a route
		$app->get('/myroute', function (Application $app) {
			return new Response('Hello world!');
		});

	}
}
```

Of course, both the `SlimFrameworkModule` and the `SampleModule` must be registered in `app.php`:

**app.php**
```php
$app = new Application(
    [
        $slim = new SlimFrameworkModule(),
        new SampleModule($slim)
    ]
);
```

Notice how the `SampleModule` module is passed a reference to the `SlimFrameworkModule` class.

##Things you should know

A Slim application embeds a Pimple container. This container will be shared with all the other modules.
Therefore, all objects you put in the Slim application will be available from the root container.

## Prefixing the Slim container

If you plan to work with several frameworks (from instance with Slim and Symfony 2), you may run into
problems regarding identifiers collisions. Indeed, both Slim and Symfony 2 have their containers, and they are
using the same instance names for different purposes.

In order to avoid this namespace clashes, you can **prefix** all instances stored into Slim using the `$prefix`
parameter in the `SlimFrameworkModule` class.

**app.php**
```php
$app = new Application(
    [
        $slim = new SlimFrameworkModule('slim.'),
        new SampleModule($slim)
    ]
);
```

Notice the 'slim.' parameter passed to the `SlimFrameworkModule`. When accessing an instance stored into
Slim from the root container, you will have to prefix it with 'slim.'.

For instance:

```php
// We store an instance in Slim
$slimApp['my_controller'] = $slimApp->share(function($c) { new MyController() });

// We retrieve it from the root container using the prefix!
$rootContainer->get('slim.my_controller');
```
