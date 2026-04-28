<?php

/**
 * Front controller
 *
 * PHP version 7.0
 */

/**
 * Composer
 */
require dirname(__DIR__) . '/vendor/autoload.php';


/**
 * Error and Exception handling
 */
error_reporting(E_ALL);
set_error_handler('Core\Error::errorHandler');
set_exception_handler('Core\Error::exceptionHandler');


/**
 * Sessions
 */


ini_set('session.cookie_lifetime', 60 * 60 * 24 * 30); 
ini_set('session.gc_maxlifetime', 60 * 60 * 24 * 30);
ini_set('session.save_path', '../session');
session_start();


/**
 * Routing
 */
$router = new Core\Router();

// var_dump($_SERVER['REQUEST_URI']);
// exit;

// Add the routes
$router->add('api/expenses/summary/{category:[a-zA-Z0-9ąćęłńóśżźĄĆĘŁŃÓŚŻŹ]+}/{year:\d+}/{month:\d+}',['controller' => 'PersonalBudget', 'action' => 'dateLimitSumExpense']);
$router->add('api/limit/{category:[a-zA-Z0-9ąćęłńóśżźĄĆĘŁŃÓŚŻŹ]+}', ['controller' => 'PersonalBudget', 'action' => 'limit']);
$router->add('', ['controller' => 'Home', 'action' => 'index']);
$router->add('login', ['controller' => 'Login', 'action' => 'new']);
$router->add('logout', ['controller' => 'Login', 'action' => 'destroy']);
$router->add('password/reset/{token:[\da-f]+}', ['controller' => 'Password', 'action' => 'reset']);
$router->add('signup/activate/{token:[\da-f]+}', ['controller' => 'Signup', 'action' => 'activate']);
$router->add(
    'personalbudget/successareyousuredeletefromincomes/{idincomesdelete:\d+}/{myordinalnumberdeleteincomesvar:\d+}',
    [
        'controller' => 'PersonalBudget',
        'action' => 'successAreyouSuredeleteFromIncomes'
    ]
);
$router->add('{controller}/{action}');

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($path, '/');

// echo "<pre>";
// echo "REQUEST_URI: "; var_dump($_SERVER['REQUEST_URI']);
// echo "PARSED PATH: "; var_dump($path);
// echo "ROUTES: "; var_dump($router->getRoutes());
// echo "</pre>";
// exit;

// Przekazanie poprawnej ścieżki do routera
$router->dispatch($path);


// $router->dispatch($_SERVER['QUERY_STRING']);


////personalbudget/successareyousuredeletefromincomes/214/3