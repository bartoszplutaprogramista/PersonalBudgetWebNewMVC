<?php

namespace Core;

use \App\Auth;
use \App\Models\User;

/**
 * View
 *
 * PHP version 7.0
 */
class View
{

    /**
     * Render a view file
     *
     * @param string $view  The view file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function render($view, $args = [])
    {
        extract($args, EXTR_SKIP);

        $file = dirname(__DIR__) . "/App/Views/$view";  // relative to Core directory

        if (is_readable($file)) {
            require $file;
        } else {
            throw new \Exception("$file not found");
        }
    }

    /**
     * Render a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return void
     */
    public static function renderTemplate($template, $args = [])
    {
        echo static::getTemplate($template, $args);
    }

    /**
     * Get the contents of a view template using Twig
     *
     * @param string $template  The template file
     * @param array $args  Associative array of data to display in the view (optional)
     *
     * @return string
     */
    public static function getTemplate($template, $args = [])
    {
        static $twig = null;


        $userValue = Auth::getUser();
        // // if($mmm = Auth::getUser())
        // // {

            if($userValue !== null)
            {
                $array = get_object_vars($userValue);
                $user_object = new User($_POST);
                $userId = $user_object->getUserId($array['email']);
            }

        // print_r ($mmm);

        

        // $moja = \App\Models\ModelPersonalBudget::getQueryNameIncome($mmm);

        // print_r ($moja);

            if ($twig === null) {
                $loader = new \Twig\Loader\FilesystemLoader(dirname(__DIR__) . '/App/Views');
                $twig = new \Twig\Environment($loader);
                //$loader = new \Twig_Loader_Filesystem(dirname(__DIR__) . '/App/Views');
                //$twig = new \Twig_Environment($loader);
                $twig->addGlobal('current_user', \App\Auth::getUser());
                $twig->addGlobal('flash_messages', \App\Flash::getMessages());
                if($userValue !== null){
                    $twig->addGlobal('query_name_income_current_year', \App\Models\ModelPersonalBudget::getQueryNameIncomeCurrentYear($userId));
                    $twig->addGlobal('query_name_expense_current_year', \App\Models\ModelPersonalBudget::getQueryNameExpenseCurrentYear($userId));
                    $twig->addGlobal('query_name_incomes_sum_current_year', \App\Models\ModelPersonalBudget::incomesSumCurrentYear($userId));
                    $twig->addGlobal('query_name_expenses_sum_current_year', \App\Models\ModelPersonalBudget::expensesSumCurrentYear($userId));
                    $twig->addGlobal('date_from_to_current_year', \App\Controllers\personalBudget::dateFromToCurrentYear());

                }
            }

            return $twig->render($template, $args);
        // }
    }
}
