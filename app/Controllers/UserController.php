<?php

namespace App\Controllers;

use App\Exceptions\AuthException;
use App\Exceptions\EmailException;
use App\Exceptions\EmptyFieldsException;
use App\Exceptions\PasswordConfirmException;
use App\Exceptions\PasswordException;
use App\Exceptions\UsernameException;
use App\Services\AuthService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class UserController extends Controller
{
    public function getIndexAction(Request $request)
    {
        try {
            $device = $request->headers->get('User-Agent');
            $user = AuthService::authByToken($request->cookies->get(AuthService::COOKIE_TOKEN_NAME), $device);
        } catch (AuthException $e) {
            return new RedirectResponse('/login');
        }
        return $this->render('home.twig', [
            'auth' => true, 'user' => $user
        ]);
    }

    public function getRegisterAction(Request $request)
    {
        try {
            $device = $request->headers->get('User-Agent');
            AuthService::authByToken($request->cookies->get(AuthService::COOKIE_TOKEN_NAME), $device);
            return new RedirectResponse('/');
        } catch (AuthException $e) {
            return $this->render('auth/register.twig');
        }
    }

    public function postRegisterAction(Request $request)
    {
        $isJson = $request->get('format') === 'json';
        $email = trim((string)$request->get('email'));
        $password = trim((string)$request->get('password'));
        $passwordConfirm = trim((string)$request->get('password_confirmation'));
        $name = trim((string)$request->get('name'));
        try {
            $this->validatePassword($password);
            $this->validateEmail($email);
            $this->validateName($name);
            if ($passwordConfirm !== $password) {
                throw new PasswordConfirmException();
            }
            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'ip' => $request->getClientIp()
            ];
            $device = $request->headers->get('User-Agent');
            AuthService::register($userData, $device);
            if ($isJson) {
                return new JsonResponse(json_encode(['success' => true], true));
            }
            return new RedirectResponse('/');
        } catch (AuthException $e) {
            if ($isJson) {
                return new JsonResponse(json_encode([
                    'success' => false,
                    'error' => $e->getDefaultMessage()
                ], true));
            }
            $session = new Session();
            $session->getFlashBag()->setAll([
                'error' => $e->getDefaultMessage(),
                'old' => [
                    'email' => $email,
                    'name' => $name
                ]
            ]);
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }

    }

    public function getLoginAction(Request $request)
    {
        try {
            $device = $request->headers->get('User-Agent');
            $user = AuthService::authByToken($request->cookies->get(AuthService::COOKIE_TOKEN_NAME), $device);
            if (!empty($user)) {
                return new RedirectResponse('/');
            }
        } finally {
            return $this->render('auth/login.twig', [
                'auth' => false
            ]);
        }
    }

    public function postLoginAction(Request $request)
    {
        $isJson = $request->get('format') === 'json';
        $email = trim((string)$request->get('email'));
        $password = trim((string)$request->get('password'));
        $remember = !!$request->get('remember');
        try {
            $this->validateEmail($email);
            $this->validatePassword($password);
            $device = $request->headers->get('User-Agent');
            $userData = AuthService::authByPassword($email, $password, $device, $remember);
            if ($isJson) {
                return new JsonResponse(json_encode(['success' => true], true));
            }
            return $this->render('home.twig', [
                'auth' => true,
                'user' => $userData
            ]);
        } catch (AuthException $e) {
            if ($isJson) {
                return new JsonResponse(json_encode([
                    'success' => false,
                    'error' => $e->getDefaultMessage()
                ], true));
            }
            $session = new Session();
            $session->getFlashBag()->setAll([
                'error' => $e->getDefaultMessage(),
                'old' => [
                    'email' => $email,
                    'remember' => $remember
                ]
            ]);
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }

    }

    public function postLogoutAction(Request $request)
    {
        AuthService::logout($request->cookies->get(AuthService::COOKIE_TOKEN_NAME));
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    private function validateEmail($email)
    {
        if (empty($email)) {
            throw new EmptyFieldsException();
        }
        if (!preg_match(AuthService::EMAIL_REGEXP, $email)) {
            throw new EmailException();
        }
    }

    private function validatePassword($password)
    {
        if (empty($password)) {
            throw new EmptyFieldsException();
        }
        if (strlen($password) < AuthService::PASSWORD_MIN_LENGTH || strlen($password) > AuthService::PASSWORD_MAX_LENGTH) {
            throw new PasswordException();
        }
    }

    private function validateName($name)
    {
        if (empty($name)) {
            throw new EmptyFieldsException();
        }
        if (strlen($name) < AuthService::USERNAME_MIN_LENGTH || strlen($name) > AuthService::USERNAME_MAX_LENGTH) {
            throw new UsernameException();
        }
    }

}
