<?php

namespace Src;

use FastRoute\RouteCollector;
use FastRoute\RouteParser\Std;
use FastRoute\DataGenerator\MarkBased;
use FastRoute\Dispatcher\MarkBased as Dispatcher;
use Src\Request;
use Src\Traits\SingletonTrait;

class Middleware
{
    use SingletonTrait;

    private RouteCollector $middlewareCollector;

    public function add($httpMethod, string $route, array $action): void
    {
        $this->middlewareCollector->addRoute($httpMethod, $route, $action);
    }

    public function group(string $prefix, callable $callback): void
    {
        $this->middlewareCollector->addGroup($prefix, $callback);
    }

    private function __construct()
    {
        $this->middlewareCollector = new RouteCollector(
            new Std(),
            new MarkBased(),
        );
    }

    public function go(
        string $httpMethod,
        string $uri,
        Request $request,
    ): Request {
        return $this->runMiddlewares(
            $httpMethod,
            $uri,
            $this->runAppMiddlewares($request),
        );
    }

    private function runMiddlewares(
        string $httpMethod,
        string $uri,
        Request $request,
    ): Request {
        $routeMiddleware = app()->settings->app["routeMiddleware"];
        foreach (
            $this->getMiddlewaresForRoute($httpMethod, $uri)
            as $middleware
        ) {
            $args = explode(":", $middleware);
            $request =
                new ($routeMiddleware[$args[0]])()->handle(
                    $request,
                    $args[1] ?? null,
                ) ?? $request;
        }
        return $request;
    }
    private function runAppMiddlewares(Request $request): Request
    {
        if (!isset(app()->settings->app["routeAppMiddleware"])) {
            return $request;
        }
        $routeAppMiddleware = app()->settings->app["routeAppMiddleware"];
        foreach ($routeAppMiddleware as $name => $class) {
            $request = new $class()->handle($request) ?? $request;
        }
        return $request;
    }

    private function getMiddlewaresForRoute(
        string $httpMethod,
        string $uri,
    ): array {
        $dispatcherMiddleware = new Dispatcher(
            $this->middlewareCollector->getData(),
        );
        return $dispatcherMiddleware->dispatch($httpMethod, $uri)[1] ?? [];
    }
}
