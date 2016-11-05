<?php

namespace Kernel;

use App\Exceptions\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Request;

class UrlMatcher
{

    protected $routes;
    protected $request;

    public function __construct(array $routes, Request $request)
    {
        $this->routes = $routes;
        $this->request = $request;
    }

    public function match() {
        $matchedRoute = [];
        foreach($this->routes as $route) {
            if ($route['url'] === $this->request->getPathInfo() && $route['method'] === $this->request->getMethod()) {
                $matchedRoute = $route;
            }
        }
        if (empty($matchedRoute)) {
            throw new ResourceNotFoundException();
        }
        return $matchedRoute;
    }

}
