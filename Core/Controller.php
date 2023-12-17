<?php

namespace Core;

use \App\Auth;
use \App\Flash;

abstract class Controller
{

    protected $route_params = [];

    public function __construct($route_params)
    {
        $this->route_params = $route_params;
    }

    public function __call($name, $args)
    {
        $method = $name . 'Action';

        if (method_exists($this, $method)) {
            if ($this->before() !== false) {
                call_user_func_array([$this, $method], $args);
                $this->after();
            }
        } else {
            throw new \Exception("Method $method not found in controller " . get_class($this));
        }
    }

    protected function before()
    {
    }

    protected function after()
    {
    }

    public function redirect($url)
    {
        header('Location: http://' . $_SERVER['HTTP_HOST'] . $url, true, 303);
        exit;
    }
    public function requireLogin()
    {
        if (! Auth::getUser()) {
            $url1="/personalbudget/addincome";
            $url2="/personalbudget/addexpense";
            $url3="/personalbudget/browsethebalance";
            $url4="/profile/show";
            $url5="/profile/edit";

            Flash::addMessage('Zaloguj się aby uzyskać dostęp do tej strony', Flash::INFO);

            if (($_SERVER['REQUEST_URI'])===($url1)||
            ($_SERVER['REQUEST_URI'])===($url2)||
            ($_SERVER['REQUEST_URI'])===($url3)||
            ($_SERVER['REQUEST_URI'])===($url4)||
            ($_SERVER['REQUEST_URI'])===($url5)){
                Auth::rememberRequestedPage();
            }

            $this->redirect('/login');
        }
    }
}
