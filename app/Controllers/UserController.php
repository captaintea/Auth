<?php

namespace App\Controllers;

use App\Exceptions\BaseException;
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
        } catch (\Exception $e) {
            return new RedirectResponse('/login');
        }
        if (empty($user)) {
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
            $user = AuthService::authByToken($request->cookies->get(AuthService::COOKIE_TOKEN_NAME), $device);
            if (!empty($user)) {
                return new RedirectResponse('/');
            }
        } finally {
            return $this->render('auth/register.twig');
        }
    }

    public function postRegisterAction(Request $request) {
        $isJson = $request->get('format') === 'json';
        $email = trim((string)$request->get('email'));
        $password = trim((string)$request->get('password'));
        $passwordConfirm = trim((string)$request->get('password_confirmation'));
        $name = trim((string)$request->get('name'));
        try {
            $this->validatePassword($password);
            $this->validateEmail($email);
            if (empty($name)) {
                throw new BaseException('All fields must be filled!');
            }
            if (strlen($name) < 3 || strlen($name) > 15) {
                throw new BaseException('Name must contain more then 3 and less then 15 characters');
            }
            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'ip' => $request->getClientIp()
            ];
            $device = $request->headers->get('User-Agent');
            $userData = AuthService::register($userData, $device);
            if ($passwordConfirm !== $password) {
                throw new BaseException('Wrong password confirmation');
            }
            if (empty($userData) || !is_array($userData)) {
                throw new BaseException('Server error');
            }
            if ($isJson) {
                return new JsonResponse(json_encode(['success' => true], true));
            }
            return new RedirectResponse('/');
        } catch (BaseException $e) {
            if ($isJson) {
                return new JsonResponse(json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ], true));
            }
            $session = new Session();
            $session->getFlashBag()->setAll([
                'error' => $e->getMessage(),
                'old' => [
                    'email' => $email,
                    'name' => $name
                ]
            ]);
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }

    }

    public function getLoginAction(Request $request) {
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

    public function postLoginAction(Request $request) {
        $isJson = $request->get('format') === 'json';
        $email = trim((string)$request->get('email'));
        $password = trim((string)$request->get('password'));
        $remember = !!$request->get('remember');
        try {
            $this->validateEmail($email);
            $this->validatePassword($password);
            $device = $request->headers->get('User-Agent');
            $userData = AuthService::authByPassword($email, $password, $device, $remember);
            if (empty($userData) || !is_array($userData)) {
                throw new BaseException('Server error');
            }
            if ($isJson) {
                return new JsonResponse(json_encode(['success' => true], true));
            }
            return $this->render('home.twig', [
                'auth' => true,
                'user' => $userData
            ]);
        } catch (BaseException $e) {
            if ($isJson) {
                return new JsonResponse(json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ], true));
            }
            $session = new Session();
            $session->getFlashBag()->setAll([
                'error' => $e->getMessage(),
                'old' => [
                    'email' => $email,
                    'remember' => $remember
                ]
            ]);
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }

    }

    public function postLogoutAction(Request $request) {
        AuthService::logout();
        $referer = $request->headers->get('referer');
        return new RedirectResponse($referer);
    }

    private function validateEmail($email) {
        if (empty($email)) {
            throw new BaseException('All fields must be filled!');
        }
        if (!preg_match(AuthService::EMAIL_REGEXP, $email)) {
            throw new BaseException('Wrong email');
        }
    }

    private function validatePassword($password) {
        if (empty($password)) {
            throw new BaseException('All fields must be filled!');
        }
        if (strlen($password)< 3 || strlen($password) > 20) {
            throw new BaseException('Password must contain more then 3 and less then 20 characters');
        }
    }

}
