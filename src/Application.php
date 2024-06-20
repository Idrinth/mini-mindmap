<?php
declare(strict_types=1);

namespace De\Idrinth\MiniMindmap;

use De\Idrinth\MiniMindmap\Controller\Error;
use De\Idrinth\MiniMindmap\Controller\NotFound;
use FastRoute\Dispatcher;
use FastRoute\RouteCollector;
use ReflectionClass;
use Throwable;
use function FastRoute\simpleDispatcher;

final class Application
{
    /**
     * @var array<string, array<string, array<int, string>>>
     */
    private array $routes = [];
    /**
     * @var array<string, string>
     */
    private array $parameters = [];
    public function register(string $method, string $path, string $class, ?string $handler = null): self
    {
        $this->routes[$method][$path] = [
            $class,
            $handler ?? strtolower($method),
        ];
        return $this;
    }
    public function parameter(string $class, string $name, mixed $value): self
    {
        $this->parameters["$class#$name"] = $value;
        return $this;
    }
    private function create(string $class): object
    {
        $ref = new ReflectionClass($class);
        if ($constructor = $ref->getConstructor()) {
            $args = [];
            foreach ($constructor->getParameters() as $parameter) {
                if (isset($this->parameters["$class#{$parameter->getName()}"])) {
                    $args[] = $this->parameters["$class#{$parameter->getName()}"];
                } elseif ($parameter->getType()->isBuiltin() && $parameter->getType()->allowsNull()) {
                    $args[] = null;
                } else {
                    $args[] = $this->create($parameter->getType()->getName());
                }
            }
            return $ref->newInstance(...$args);
        }
        return $ref->newInstance();
    }
    public function handle(string $requestURI, string $method): Result
    {
        $dispatcher = simpleDispatcher(function(RouteCollector $r) {
            foreach ($this->routes as $method => $route) {
                foreach ($route as  $path => $class) {
                    $r->addRoute($method, $path, $class);
                }
            }
        });
        $routeInfo = $dispatcher->dispatch($method, $requestURI);
        try {
            switch ($routeInfo[0]) {
                case Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    $vars = $routeInfo[2];
                    return $this->create($handler[0])->$handler[1](...$vars);
                case Dispatcher::NOT_FOUND:
                case Dispatcher::METHOD_NOT_ALLOWED:
                default:
                    return (new NotFound())->all();
            }
        } catch (NotFoundException $e) {
            return (new NotFound())->all();
        } catch (Throwable $t) {
            error_log("$t");
            return (new Error())->all();
        }
    }
}
