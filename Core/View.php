<?php

namespace Core;

use \App\Auth;
use \App\Models\User;
use \App\Models\ModelPersonalBudget;
use \App\Controllers\personalBudget;
// use \App\Controllers\personalBudget;

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

                    // $personalBudget = new personalBudget($_POST);
                    // $paymentMethodInView = $personalBudget->returnWhichPeriodOfTime();

                    // if($personalBudget->returnPaymentMethodBoolean()==true){
                    //     echo "paymentMethodPublic: ".$personalBudget->paymentMethodPublic;
                    //     exit;
                    // }
                    



                    // if($help=="currentMonth"){
                    // $paymentMethodInView = \App\Controllers\personalBudget::returnWhichMonth(); 
                    // if($paymentMethodInView == "currentMonth"){

                        $dateCurrentMonth = \App\Models\ModelPersonalBudget::getDateCurrentMonth();
                        
                        
                        //current month
                        $twig->addGlobal('date_from_to_current_month', \App\Controllers\personalBudget::dateFromToCurrentMonth());
                        $twig->addGlobal('query_name_income_current_month', \App\Models\ModelPersonalBudget::getQueryNameIncome($userId, $dateCurrentMonth));
                        $twig->addGlobal('query_name_expense_current_month', \App\Models\ModelPersonalBudget::getQueryNameExpense($userId, $dateCurrentMonth));
                        $twig->addGlobal('query_name_incomes_sum_current_month', \App\Models\ModelPersonalBudget::incomesSum($userId, $dateCurrentMonth));
                        $twig->addGlobal('query_name_expenses_sum_current_month', \App\Models\ModelPersonalBudget::expensesSum($userId, $dateCurrentMonth));
                    // }
                    // }


                    //current month
                    // $twig->addGlobal('date_from_to_current_month', \App\Controllers\personalBudget::dateFromToCurrentMonth());
                    // $twig->addGlobal('query_name_income_current_month', \App\Models\ModelPersonalBudget::getQueryNameIncomeCurrentMonth($userId));
                    // $twig->addGlobal('query_name_expense_current_month', \App\Models\ModelPersonalBudget::getQueryNameExpenseCurrentMonth($userId));
                    // $twig->addGlobal('query_name_incomes_sum_current_month', \App\Models\ModelPersonalBudget::incomesSumCurrentMonth($userId));
                    // $twig->addGlobal('query_name_expenses_sum_current_month', \App\Models\ModelPersonalBudget::expensesSumCurrentMonth($userId));
                    // // }
                    

                    //last month

                    $dateLastMonth = \App\Models\ModelPersonalBudget::getDateLastMonth();

                    $twig->addGlobal('date_from_to_last_month', \App\Controllers\personalBudget::dateFromToLastMonth());
                    $twig->addGlobal('query_name_income_last_month', \App\Models\ModelPersonalBudget::getQueryNameIncome($userId, $dateLastMonth));
                    $twig->addGlobal('query_name_expense_last_month', \App\Models\ModelPersonalBudget::getQueryNameExpense($userId, $dateLastMonth));
                    $twig->addGlobal('query_name_incomes_sum_last_month', \App\Models\ModelPersonalBudget::incomesSum($userId, $dateLastMonth));
                    $twig->addGlobal('query_name_expenses_sum_last_month', \App\Models\ModelPersonalBudget::expensesSum($userId, $dateLastMonth));
                    

                    //current year

                    $dateCurrentYear = \App\Models\ModelPersonalBudget::getDateCurrentYear();

                    $twig->addGlobal('date_from_to_current_year', \App\Controllers\personalBudget::dateFromToCurrentYear());
                    $twig->addGlobal('query_name_income_current_year', \App\Models\ModelPersonalBudget::getQueryNameIncome($userId, $dateCurrentYear));
                    $twig->addGlobal('query_name_expense_current_year', \App\Models\ModelPersonalBudget::getQueryNameExpense($userId, $dateCurrentYear));
                    $twig->addGlobal('query_name_incomes_sum_current_year', \App\Models\ModelPersonalBudget::incomesSum($userId, $dateCurrentYear));
                    $twig->addGlobal('query_name_expenses_sum_current_year', \App\Models\ModelPersonalBudget::expensesSum($userId, $dateCurrentYear));
                    


                   

                }
            }

            return $twig->render($template, $args);
        // }
    }
}
