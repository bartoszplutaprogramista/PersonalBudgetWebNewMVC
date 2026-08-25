<?php

namespace Core;

use \App\Auth;
use \App\Models\User;
use \App\Models\ModelPersonalBudget;
use \App\Controllers\personalBudget;

class View
{

    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__) . "/App/Views/$view";  

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    public static function renderTemplate($template, $args = [])
    {
        echo static::getTemplate($template, $args);
    }

    public static function getTemplate($template, $args = [])
    {
        static $twig = null;


        $userValue = Auth::getUser();

              if($userValue && is_object($userValue))
            {
                $array = get_object_vars($userValue);
                $user_object = new User($_POST);
                $userId = $user_object->getUserId($array['email']);
                $_SESSION['userIdSession'] = $userId;
            }

            if ($twig === null) {
                $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');
                $twig = new \Twig\Environment($loader);

                $twig->addGlobal('current_user', \App\Auth::getUser());
                $twig->addGlobal('flash_messages', \App\Flash::getMessages());
            }

            return $twig->render($template, $args);
    }
}
