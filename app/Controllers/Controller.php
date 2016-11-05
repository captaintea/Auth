<?php

namespace App\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Twig_Environment;
use Twig_Loader_Filesystem;
use Twig_SimpleFunction;

class Controller
{
    public function render($viewPath, $data = [])
    {
        $loader = new Twig_Loader_Filesystem($_SERVER['DOCUMENT_ROOT'] . '/../' . config('views.paths', 'resources/views'));
        $twig = new Twig_Environment($loader, [
            'cache' => $_SERVER['DOCUMENT_ROOT'] . '/../' . config('views.compiled' , 'storage/views'),
        ]);
        $function = new Twig_SimpleFunction('config', function ($key, $default = null) {
            return config($key, $default);
        });
        $twig->addFunction($function);
        $function = new Twig_SimpleFunction('elixir', function ($file) {
            return elixir($file);
        });
        $twig->addFunction($function);
        $session = new Session();
        $twig->addGlobal('flash', $session->getFlashBag()->all());
        $template = $twig->loadTemplate($viewPath);
        return new Response($template->render($data));
    }

}
