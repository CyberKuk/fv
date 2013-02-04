### Routing

Routing bundle used for convert `fv\Http\Request` to object to `fv\Http\Response`.

## Kernel

Routing main request by kernel:

    use Bundle\fv\RoutingBundle\Kernel;

    $kernel = new Kernel();
    $response = $kernel->handle();
    $response->send();

Also you can handle yor custom request:

    $request = new fv\Http\Request();
    <-- fill request object -->
    $kernel = new Kernel( $request );

Kernel automatically create `Bundle\fv\Routing\Router` to handle request.
Router will be configured with `routes` config (empty context will be used).
See `fv\Config` for more information about configs and config contexts.



## Redirecting to Application

Kernel router assumes to route request to some application.
Although, you can return response by use your custom Route object, but it's not recommended.

To route kernel request to some application you can use `Bundle\fv\RoutingBundle\Route\PrefixRoute` which
react on uri prefix.

Config `routes.yml` example:

    back:
      class: Prefix
      prefix: /admin
      application: backend

    default:
      class: Prefix
      prefix: /
      application: frontend

If request uri starts with `/admin/*` request will be redirect to `backend` application.



## Create application

To create application you must create `applications` config in empty context.

Applications yml example:

    frontend:
      namespace: Application\Frontend
      path: apps/frontend

    backend:
      namespace: Application\Backend
      path: apps/backend



## Application

Create in application path folder `src` with class Application:

    <?php

    namespace Application\Frontend;

    use Bundle\fv\RoutingBundle\Application\AbstractApplication;

    class Application extends AbstractApplication {

    }

Application use Router with `routes` config (in application context) to handle requests to controllers.

Application `routes.yml` example:

    test:
        url: /hello/{$name}
        controller: index

    default:
        class: UriBasedController


* `/hello/foo` --> ApplicationNamespace\Controller\IndexController with `"name" => "foo"` param
* `/hello/bar` --> ApplicationNamespace\Controller\IndexController with `"name" => "bar"` param
* All other request will redirected to
    * / --> ApplicationNamespace\Controller\IndexController
    * /other --> ApplicationNamespace\Controller\OtherController
    * /some/other --> ApplicationNamespace\Controller\Some\OtherController



## Controller

Controller in fv framework means PageController. Unlike other php frameworks with Controller means `SiteModuleController`.
In request processing controller will calls method corresponding with HTTP-method.