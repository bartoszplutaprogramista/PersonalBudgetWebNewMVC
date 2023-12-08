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

            if($userValue !== null)
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
                if($userValue !== null){

                    if((isset($_SESSION['paymentMethod']))&&($_SESSION['paymentMethod']=='currentMonth')){
                        $dateCurrentMonth = \App\Models\ModelPersonalBudget::getDateCurrentMonth();
                        
                        $twig->addGlobal('date_from_to_current_month', \App\Controllers\personalBudget::dateFromToCurrentMonth());
                        $twig->addGlobal('query_name_income_current_month', \App\Models\ModelPersonalBudget::getQueryNameIncome($dateCurrentMonth));
                        $twig->addGlobal('query_name_expense_current_month', \App\Models\ModelPersonalBudget::getQueryNameExpense($dateCurrentMonth));
                        $twig->addGlobal('query_name_incomes_sum_current_month', \App\Models\ModelPersonalBudget::incomesSum($dateCurrentMonth));
                        $twig->addGlobal('query_name_expenses_sum_current_month', \App\Models\ModelPersonalBudget::expensesSum($dateCurrentMonth));
                        $twig->addGlobal('chart_incomes_current_month', \App\Models\ModelPersonalBudget::sumOfNamesFromIncomesToChart($dateCurrentMonth));
                        $twig->addGlobal('chart_expenses_current_month', \App\Models\ModelPersonalBudget::sumOfNamesFromExpensesToChart($dateCurrentMonth));
                    }

                    else if((isset($_SESSION['paymentMethod']))&&($_SESSION['paymentMethod']=='lastMonth')){
                        $dateLastMonth = \App\Models\ModelPersonalBudget::getDateLastMonth();

                        $twig->addGlobal('date_from_to_last_month', \App\Controllers\personalBudget::dateFromToLastMonth());
                        $twig->addGlobal('query_name_income_last_month', \App\Models\ModelPersonalBudget::getQueryNameIncome($dateLastMonth));
                        $twig->addGlobal('query_name_expense_last_month', \App\Models\ModelPersonalBudget::getQueryNameExpense($dateLastMonth));
                        $twig->addGlobal('query_name_incomes_sum_last_month', \App\Models\ModelPersonalBudget::incomesSum($dateLastMonth));
                        $twig->addGlobal('query_name_expenses_sum_last_month', \App\Models\ModelPersonalBudget::expensesSum($dateLastMonth));
                        $twig->addGlobal('chart_incomes_last_month', \App\Models\ModelPersonalBudget::sumOfNamesFromIncomesToChart($dateLastMonth));
                        $twig->addGlobal('chart_expenses_last_month', \App\Models\ModelPersonalBudget::sumOfNamesFromExpensesToChart($dateLastMonth));
                    }
                    

                    else if((isset($_SESSION['paymentMethod']))&&($_SESSION['paymentMethod']=='currentYear')){
                        $dateCurrentYear = \App\Models\ModelPersonalBudget::getDateCurrentYear();

                        $twig->addGlobal('date_from_to_current_year', \App\Controllers\personalBudget::dateFromToCurrentYear());
                        $twig->addGlobal('query_name_income_current_year', \App\Models\ModelPersonalBudget::getQueryNameIncome($dateCurrentYear));
                        $twig->addGlobal('query_name_expense_current_year', \App\Models\ModelPersonalBudget::getQueryNameExpense($dateCurrentYear));
                        $twig->addGlobal('query_name_incomes_sum_current_year', \App\Models\ModelPersonalBudget::incomesSum($dateCurrentYear));
                        $twig->addGlobal('query_name_expenses_sum_current_year', \App\Models\ModelPersonalBudget::expensesSum($dateCurrentYear));
                        $twig->addGlobal('chart_incomes_current_year', \App\Models\ModelPersonalBudget::sumOfNamesFromIncomesToChart($dateCurrentYear));
                        $twig->addGlobal('chart_expenses_current_year', \App\Models\ModelPersonalBudget::sumOfNamesFromExpensesToChart($dateCurrentYear));
                    }
                    
                    if(isset($_SESSION['selectedPeriod'])){
                        $twig->addGlobal('start_date_selected_period', \App\Models\ModelPersonalBudget::getStartDateSelectedPeriod()); 
                        $twig->addGlobal('end_date_selected_period', \App\Models\ModelPersonalBudget::getEndDateSelectedPeriod()); 
            
                        $twig->addGlobal('query_name_incomes_selected_period', \App\Models\ModelPersonalBudget::getSelectedPeriodQueryNameIncome());
                        $twig->addGlobal('query_name_expenses_selected_period', \App\Models\ModelPersonalBudget::getSelectedPeriodQueryNameExpense());
                        $twig->addGlobal('query_name_incomes_sum_selected_period', \App\Models\ModelPersonalBudget::incomesSelectedPeriodSum());
                        $twig->addGlobal('query_name_expenses_sum_selected_period', \App\Models\ModelPersonalBudget::expensesSelectedPeriodSum());
                        $twig->addGlobal('chart_incomes_selected_period', \App\Models\ModelPersonalBudget::sumOfNamesFromIncomesToChartSelectedPeriod());
                        $twig->addGlobal('chart_expenses_selected_period', \App\Models\ModelPersonalBudget::sumOfNamesFromExpensesToChartSelectedPeriod());
                    }
                    if(isset($_SESSION['idExpensesDelete'])){
                        $twig->addGlobal('id_expenses_delete', $_SESSION['idExpensesDelete']);
                        $twig->addGlobal('data_to_are_you_sure_table_expenses', \App\Models\ModelPersonalBudget::selectAllFromExpensesToEdit($_SESSION['idExpensesDelete']));
                    } 

                    if(isset($_SESSION['idIncomesDelete'])){
                        $twig->addGlobal('id_incomes_delete', $_SESSION['idIncomesDelete']);
                        $twig->addGlobal('data_to_are_you_sure_table_incomes', \App\Models\ModelPersonalBudget::selectAllFromIncomesToEdit($_SESSION['idIncomesDelete']));
                    } 

                    if(isset($_SESSION['idExpensesEditRow'])){
                        $twig->addGlobal('expenses_edit_values', \App\Models\ModelPersonalBudget::selectAllFromExpensesToEdit($_SESSION['idExpensesEditRow']));
                    } 

                    if(isset($_SESSION['idIncomesEditRow'])){
                        $twig->addGlobal('incomes_edit_values', \App\Models\ModelPersonalBudget::selectAllFromIncomesToEdit($_SESSION['idIncomesEditRow']));
                    } 

                    if(isset($_SESSION['myOrdinalNumberDeleteIncomesVar'])){
                        $twig->addGlobal('ordinal_delete_incomes_number', $_SESSION['myOrdinalNumberDeleteIncomesVar']);
                    }  

                    if(isset($_SESSION['myOrdinalNumberDeleteExpensesVar'])){
                        $twig->addGlobal('ordinal_delete_expenses_number', $_SESSION['myOrdinalNumberDeleteExpensesVar']);
                    } 

                    if(isset($_SESSION['paymentMethod'])){
                        $twig->addGlobal('which_period', $_SESSION['paymentMethod']);
                    }                  
                }
            }

            return $twig->render($template, $args);
    }
}
