<?php

use Kernel\UrlMatcher;
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';

$routes = include __DIR__.'/../routes/web.php';

$request = Request::createFromGlobals();

$matcher = new UrlMatcher($routes, $request);
$kernel = new Kernel\Kernel($matcher);

$response = $kernel->handle($request);

$response->send();
