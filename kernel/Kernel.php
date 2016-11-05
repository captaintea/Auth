<?php

namespace Kernel;

use App\Exceptions\ResourceNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Kernel {

    protected $matcher;

    public function __construct(UrlMatcher $matcher)
    {
        $this->matcher = $matcher;
    }

    public function handle(Request $request)
    {
        try {
            $route = $this->matcher->match();
            $request->attributes->add($route);
            if (!class_exists($route['controller'])) {
                throw new ResourceNotFoundException('Controller doest not exist');
            }
            $controller = new $route['controller']();
            if (!method_exists($controller, $action = $route['action'])) {
                throw new ResourceNotFoundException('Method doest not exist');
            }
            return $controller->$action($request);
        } catch (ResourceNotFoundException $e) {
           return new Response($e->getMessage(), 404);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }

}
