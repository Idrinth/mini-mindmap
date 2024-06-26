<?php
declare(strict_types=1);

namespace De\Idrinth\MiniMindmap;

use De\Idrinth\MiniMindmap\Controller\Error;
use De\Idrinth\MiniMindmap\Controller\NotFound;
use De\Idrinth\MiniMindmap\Result\Html;
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
                } elseif ($parameter->getType()->isBuiltin() && $parameter->isDefaultValueAvailable()) {
                    $args[] = $parameter->getDefaultValue();
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
        if ($requestURI !== '/setup' && ! preg_match('/\.[a-z]+$/ui', $requestURI) && ! is_file(dirname(__DIR__) . '/.env')) {
            $result = new Html();
            $result->setStatusCode(303);
            $result->addHeader('Location', '/setup');
            $result->setContent(['template' => 'empty.twig']);
            return $result;
        }
        if (apache_request_headers()['content-type'] ?? '' === 'application/json') {
            $_POST = json_decode(file_get_contents('php://input'), true);
        }
        $dispatcher = simpleDispatcher(function(RouteCollector $r) {
            foreach ($this->routes as $method => $route) {
                foreach ($route as  $path => $class) {
                    $r->addRoute($method, $path, $class);
                }
            }
        });
        $routeInfo = $dispatcher->dispatch(strtolower($method), $requestURI);
        try {
            switch ($routeInfo[0]) {
                case Dispatcher::FOUND:
                    $handler = $routeInfo[1];
                    $vars = $routeInfo[2] ?? [];
                    return $this->create($handler[0])->{$handler[1]}(...array_values($vars));
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
