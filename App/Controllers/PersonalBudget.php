<?php
//Personal Budget
namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\ModelPersonalBudget;
use \App\Models\User;


#[\AllowDynamicProperties]
class Personalbudget extends Authenticated
{
    public $user;

    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

    public function addIncomeAction()
    {
         $incomes_options_form = \App\Models\ModelPersonalBudget::selectOptionsForIncomes();

        View::renderTemplate('PersonalBudget/addIncome.html', [
            'user' => $this->user,
            'incomes_options_form' => $incomes_options_form
        ]);
    }

    public function addExpenseAction()
    {
        $expenses_options_form_category = \App\Models\ModelPersonalBudget::selectOptionsForExpensesCategory();           
        $expenses_options_form_payment_method = \App\Models\ModelPersonalBudget::selectOptionsForExpensesPaymentMethod(); 

        View::renderTemplate('PersonalBudget/addExpense.html', [
            'user' => $this->user,
            'expenses_options_form_category' => $expenses_options_form_category,
            'expenses_options_form_payment_method' => $expenses_options_form_payment_method
        ]);
    } 

    public function browseTheBalanceAction()
    {
        View::renderTemplate('PersonalBudget/browseTheBalance.html', [
            'user' => $this->user
        ]);
    } 

    public function successDeletedExpenseAction()
    {
        View::renderTemplate('PersonalBudget/successDeletedExpense.html', [
            'user' => $this->user
        ]);
    }

    public function successAreyouSuredeleteFromIncomesAction()
    {
        $id_incomes_delete = $_SESSION['idIncomesDelete'];
        $data_to_are_you_sure_table_incomes = \App\Models\ModelPersonalBudget::selectAllFromIncomesToEdit($_SESSION['idIncomesDelete']);
        $ordinal_delete_incomes_number = $_SESSION['myOrdinalNumberDeleteIncomesVar'];
        $which_period = $_SESSION['paymentMethod'];

        View::renderTemplate('PersonalBudget/successAreYouSureDeleteFromIncomes.html', [
            'user' => $this->user,
            'id_incomes_delete' => $id_incomes_delete,
            'data_to_are_you_sure_table_incomes' => $data_to_are_you_sure_table_incomes,
            'ordinal_delete_incomes_number' => $ordinal_delete_incomes_number,
            'which_period' => $which_period
        ]);
    }

    public function successAreyouSuredeleteFromExpensesAction()
    {
        $id_expenses_delete = $_SESSION['idExpensesDelete'];
        $data_to_are_you_sure_table_expenses = \App\Models\ModelPersonalBudget::selectAllFromExpensesToEdit($_SESSION['idExpensesDelete']);
        $ordinal_delete_expenses_number = $_SESSION['myOrdinalNumberDeleteExpensesVar'];
        $which_period = $_SESSION['paymentMethod'];

        View::renderTemplate('PersonalBudget/successAreYouSureDeleteFromExpenses.html', [
            'user' => $this->user,
            'id_expenses_delete' => $id_expenses_delete,
            'data_to_are_you_sure_table_expenses' => $data_to_are_you_sure_table_expenses,
            'ordinal_delete_expenses_number' => $ordinal_delete_expenses_number,
            'which_period' => $which_period
        ]);
    }    

    // public function successEditIncomesAction()
    // {
    //     View::renderTemplate('PersonalBudget/editIncome.html', [
    //         'user' => $this->user,
    //         'incomes_edit_values' => $incomesEditValues;
    //     ]);
    // }

    // public function successEditExpensesAction()
    // {
    //     View::renderTemplate('PersonalBudget/editExpense.html', [
    //         'user' => $this->user
    //     ]);
    // }

    public function redirectToChosenPeriod(){
        if($_SESSION['paymentMethod'] == "currentMonth"){
            $this->redirect('/personalbudget/successbrowseselectedperiodcurrentmonth');
        } else if ($_SESSION['paymentMethod'] == "currentYear"){         
            $this->redirect('/personalbudget/successbrowseselectedperiodcurrentyear');
        } else if ($_SESSION['paymentMethod'] == "lastMonth"){         
            $this->redirect('/personalbudget/successbrowseselectedperiodlastmonth');
        } else {
            $this->redirect('/personalbudget/successselectedperiodchoosethedate');
        }
    }

    public function updateIncomeAction()
    {
        $personalBudget = new ModelPersonalBudget($_POST);

        if ($personalBudget->updateIncomes()) {
            Flash::addMessage('Pomyślnie zakończono edycję');
            $this->redirectToChosenPeriod();
        }
    }

    public function updateExpenseAction()
    {
        $personalBudget = new ModelPersonalBudget($_POST);

        if ($personalBudget->updateExpenses()) {
            Flash::addMessage('Pomyślnie zakończono edycję');
            $this->redirectToChosenPeriod();
        }
        unset($_SESSION['paymentMethod']);
    }

    //Po zmianie z globalnych na render w widoku
    // public function editIncomes()
    // {
    //     if(isset($_POST['editRowIncomes'])) {
    //         $_SESSION['idIncomesEditRow'] = $_POST['editRowIncomes'];
    //     }
    //     $this->redirect('/personalbudget/successeditincomes');
    // }
    public function editIncomes()
    {
        if(isset($_POST['editRowIncomes'])) {
            $_SESSION['idIncomesEditRow'] = $_POST['editRowIncomes'];
        }

        $incomesEditValues = \App\Models\ModelPersonalBudget::selectAllFromIncomesToEdit($_SESSION['idIncomesEditRow']);
        $incomes_options_form = \App\Models\ModelPersonalBudget::selectOptionsForIncomes();

        View::renderTemplate('PersonalBudget/editIncome.html', [
            'user' => $this->user,
            'incomes_edit_values' => $incomesEditValues,
            'incomes_options_form' => $incomes_options_form
        ]);


        //  $incomes_options_form = \App\Models\ModelPersonalBudget::selectOptionsForIncomes();

        // View::renderTemplate('PersonalBudget/addIncome.html', [
        //     'user' => $this->user,
        //     'incomes_options_form' => $incomes_options_form
        // ]);
    }


    public function editExpenses()
    {
        if(isset($_POST['editRow'])) {
            $_SESSION['idExpensesEditRow'] = $_POST['editRow'];
        }

        $expensesEditValues = \App\Models\ModelPersonalBudget::selectAllFromExpensesToEdit($_SESSION['idExpensesEditRow']);     
        $expenses_options_form_category = \App\Models\ModelPersonalBudget::selectOptionsForExpensesCategory();           
        $expenses_options_form_payment_method = \App\Models\ModelPersonalBudget::selectOptionsForExpensesPaymentMethod();


        View::renderTemplate('PersonalBudget/editExpense.html', [
            'user' => $this->user,
            'expenses_edit_values' => $expensesEditValues,
            'expenses_options_form_category' => $expenses_options_form_category,
            'expenses_options_form_payment_method' => $expenses_options_form_payment_method
        ]);
    }

    // public function areYouSureDeleteFromIncomes()
    // {
    //     if(isset($_POST['deleteRowIncomes'])) {
    //         $_SESSION['idIncomesDelete'] = $_POST['deleteRowIncomes'];
    //         // $id_incomes_delete = $_POST['deleteRowIncomes'];
    //     }

    //     if(isset($_POST['myOrdinalNumberDeleteIncomes'])) {
    //         $_SESSION['myOrdinalNumberDeleteIncomesVar'] = $_POST['myOrdinalNumberDeleteIncomes'];
    //         // $ordinal_delete_incomes_number = $_POST['myOrdinalNumberDeleteIncomes'];
    //     }

    //     $id_incomes_delete = $_SESSION['idIncomesDelete'];
    //     $data_to_are_you_sure_table_incomes = \App\Models\ModelPersonalBudget::selectAllFromIncomesToEdit($_SESSION['idIncomesDelete']);
    //     $ordinal_delete_incomes_number = $_SESSION['myOrdinalNumberDeleteIncomesVar'];
    //     $which_period = $_SESSION['paymentMethod'];

    //     View::renderTemplate('PersonalBudget/successAreYouSureDeleteFromIncomes.html', [
    //         'user' => $this->user,
    //     ]);
    //     $this->redirect('/personalbudget/successareyousuredeletefromincomes');
    // }

    public function areYouSureDeleteFromIncomes()
    {
        if(isset($_POST['deleteRowIncomes'])) {
            $_SESSION['idIncomesDelete'] = $_POST['deleteRowIncomes'];
        }

        if(isset($_POST['myOrdinalNumberDeleteIncomes'])) {

            $_SESSION['myOrdinalNumberDeleteIncomesVar'] = $_POST['myOrdinalNumberDeleteIncomes'];
        }

        $this->redirect('/personalbudget/successareyousuredeletefromincomes');
    }
    
    public function areYouSureDeleteFromExpenses()
    {
        if(isset($_POST['deleteRow'])) {
            $_SESSION['idExpensesDelete'] = $_POST['deleteRow'];
        }

        if(isset($_POST['myOrdinalNumberDeleteExpenses'])) {
            
            $_SESSION['myOrdinalNumberDeleteExpensesVar'] = $_POST['myOrdinalNumberDeleteExpenses'];
        }

        $this->redirect('/personalbudget/successareyousuredeletefromexpenses');
    }

    public function deleteFromIncomes()
    {
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->deleteIncome()) {
            Flash::addMessage('Pomyślnie usunięto rekord');
            $this->redirectToChosenPeriod();
         }
    }

    public function deleteFromExpenses()
    {
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->deleteExpense()) {
            Flash::addMessage('Pomyślnie usunięto rekord');
            $this->redirectToChosenPeriod();
        }
    }

    public function newIncomeAction()
    {

        $this->user = Auth::getUser();  

        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->insertToIncomes($this->user)) {
            $this->redirect('/personalbudget/successaddincome');      
        }
    }

    public function newExpenseAction()
    {
        $this->user = Auth::getUser();  
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->insertToExpenses($this->user)) {
            $this->redirect('/personalbudget/successaddexpense');      
        }
    }

    public static function dateFromToCurrentMonth()
    {
        $dataHelpYearMonth = date("Y-m");
        $currentDate=date("Y-m-d");

        $dateFromTo = $dataHelpYearMonth."-01 do ".$currentDate; 

        return $dateFromTo;
    }

    public static function dateFromToLastMonth()
    {
        $timeYear = date('Y', strtotime("-1 MONTH"));
        $timeMonth = date('m', strtotime("-1 MONTH"));
        $timeHowManyDays = date('t', strtotime("-1 MONTH"));

        $dateFromTo = $timeYear."-".$timeMonth."-01 do ".$timeYear."-".$timeMonth."-".$timeHowManyDays;

        return $dateFromTo;
    }
    
    public static function dateFromToCurrentYear()
    {
        $dateCurrentYear = date("Y");
        $currentDate=date("Y-m-d");
        $dateFromTo = 	$dateCurrentYear."-01-01 do ".$currentDate;

        return $dateFromTo;
    }

    public static function dateFromToSelectedPeriodDate1()
    {
        $dateSelectedPeriod1 = $_POST['dateSelectedPeriod1'];

        return $dateSelectedPeriod1;
    }

    public static function dateFromToSelectedPeriodDate2()
    {
        $dateSelectedPeriod2 = $_POST['dateSelectedPeriod2'];

        return $dateSelectedPeriod2;
    } 

    public function newBrowseTheBalanceAction()
    {
        $paymentMethod = $_POST['paymentMethod'];
        $_SESSION['paymentMethod'] = $paymentMethod;

        if($paymentMethod=='currentMonth')
            {
                $this->redirect('/personalbudget/successbrowseselectedperiodcurrentmonth');
            }
        elseif($paymentMethod=='lastMonth'){
            {
                $this->redirect('/personalbudget/successbrowseselectedperiodlastmonth');
            }
        }
        elseif ($paymentMethod=='currentYear'){
            {
                $this->redirect('/personalbudget/successbrowseselectedperiodcurrentyear');      
            }
        }
        elseif ($paymentMethod=='selectedPeriod'){
            $this->redirect('/personalbudget/browseselectedperiodprocessing');
        }
    } 
    
    public function newSelectedPeriod()
    {
        $dateSelectedPeriod1 = $_POST['dateSelectedPeriod1'];
        $dateSelectedPeriod2 = $_POST['dateSelectedPeriod2'];

        $_SESSION['selectedPeriod'] = "selectedPeriod";
        $_SESSION['start_date'] = $dateSelectedPeriod1;
        $_SESSION['end_date'] = $dateSelectedPeriod2;

        if($_SESSION['start_date']>$_SESSION['end_date']){
            $this->redirect('/personalbudget/choosecorrectdate');
        } else {
            $this->redirect('/personalbudget/successselectedperiodchoosethedate');  
        }      
    }

    public function dateLimitSumExpenseAction()
    {
        $category = $this->route_params['category'];
        $year     = $this->route_params['year'];
        $month    = $this->route_params['month'];

        $personalBudget = new ModelPersonalBudget();

        $sum = $personalBudget->selectSumOfExpensesForParticularCategoryAndDate(
            $category,
            $year,
            $month
        );

        echo json_encode(
            ['category' => $category, 'year' => $year, 'month' => $month, 'total' => $sum],
            JSON_UNESCAPED_UNICODE
        );
    }


//!DZIAŁA WCZEŚNIEJ
    // public function limitAction()
    // {
    //     // $user_id = $this->user->id;
    //     $category = $this->route_params['category'];

    //     // $this->user = Auth::getUser();  
    //     $personalBudget = new ModelPersonalBudget($_POST);
    //     // if ($personalBudget->selectValueOfLimit($this->user)) {
    //     //     $this->redirect('/personalbudget/successaddexpense');      
    //     // }

    //     // echo json_encode(ExpenseCategory::getLimit($user_id, $category), JSON_UNESCAPED_UNICODE);
    //     echo json_encode($personalBudget->selectLimitValueUserIdCategoryName($category), JSON_UNESCAPED_UNICODE);
    // }
    public function limitAction()
    {
        $category = $this->route_params['category'];

        $personalBudget = new ModelPersonalBudget();

        $limit = $personalBudget->selectLimitValueUserIdCategoryName($category);

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            ['limit' => $limit],
            JSON_UNESCAPED_UNICODE
        );
    }



    public function choosecorrectdateAction()
    {
        View::renderTemplate('PersonalBudget/chooseCorrectDate.html', [
            'user' => $this->user
        ]);
    }

    public function successBrowseSelectedPeriodCurrentMonthAction()
    {
        

            $dateCurrentMonth = \App\Models\ModelPersonalBudget::getDateCurrentMonth();
                        
            
            $date_from_to_current_month = \App\Controllers\Personalbudget::dateFromToCurrentMonth();
            $query_name_income_current_month = \App\Models\ModelPersonalBudget::getQueryNameIncome($dateCurrentMonth);
            $query_name_expense_current_month = \App\Models\ModelPersonalBudget::getQueryNameExpense($dateCurrentMonth);
            $query_name_incomes_sum_current_month = \App\Models\ModelPersonalBudget::incomesSum($dateCurrentMonth);
            $query_name_expenses_sum_current_month = \App\Models\ModelPersonalBudget::expensesSum($dateCurrentMonth);
            $chart_incomes_current_month = \App\Models\ModelPersonalBudget::sumOfNamesFromIncomesToChart($dateCurrentMonth);
            $chart_expenses_current_month = \App\Models\ModelPersonalBudget::sumOfNamesFromExpensesToChart($dateCurrentMonth);

            View::renderTemplate('PersonalBudget/browseSelectedPeriodCurrentMonth.html', [
                'user' => $this->user,
                'date_from_to_current_month' => $date_from_to_current_month,
                'query_name_income_current_month' => $query_name_income_current_month,
                'query_name_expense_current_month' => $query_name_expense_current_month,
                'query_name_incomes_sum_current_month' => $query_name_incomes_sum_current_month,
                'query_name_expenses_sum_current_month' => $query_name_expenses_sum_current_month,
                'chart_incomes_current_month' => $chart_incomes_current_month,
                'chart_expenses_current_month' => $chart_expenses_current_month
        ]);
    }

    public function successBrowseSelectedPeriodLastMonthAction()
    {
        $dateLastMonth = \App\Models\ModelPersonalBudget::getDateLastMonth();

            $date_from_to_last_month = \App\Controllers\Personalbudget::dateFromToLastMonth();
            $query_name_income_last_month = \App\Models\ModelPersonalBudget::getQueryNameIncome($dateLastMonth);
            $query_name_expense_last_month = \App\Models\ModelPersonalBudget::getQueryNameExpense($dateLastMonth);
            $query_name_incomes_sum_last_month = \App\Models\ModelPersonalBudget::incomesSum($dateLastMonth);
            $query_name_expenses_sum_last_month = \App\Models\ModelPersonalBudget::expensesSum($dateLastMonth);
            $chart_incomes_last_month = \App\Models\ModelPersonalBudget::sumOfNamesFromIncomesToChart($dateLastMonth);
            $chart_expenses_last_month = \App\Models\ModelPersonalBudget::sumOfNamesFromExpensesToChart($dateLastMonth);

            View::renderTemplate('PersonalBudget/browseSelectedPeriodLastMonth.html', [
                'user' => $this->user,
                'date_from_to_last_month' => $date_from_to_last_month,
                'query_name_income_last_month' => $query_name_income_last_month,
                'query_name_expense_last_month' => $query_name_expense_last_month,
                'query_name_incomes_sum_last_month' => $query_name_incomes_sum_last_month,
                'query_name_expenses_sum_last_month' => $query_name_expenses_sum_last_month,
                'chart_incomes_last_month' => $chart_incomes_last_month,
                'chart_expenses_last_month' => $chart_expenses_last_month
        ]);
    }

    public function successBrowseSelectedPeriodCurrentYearAction()
    {
        $dateCurrentYear = \App\Models\ModelPersonalBudget::getDateCurrentYear();

        $date_from_to_current_year = \App\Controllers\Personalbudget::dateFromToCurrentYear();
        $query_name_income_current_year = \App\Models\ModelPersonalBudget::getQueryNameIncome($dateCurrentYear);
        $query_name_expense_current_year = \App\Models\ModelPersonalBudget::getQueryNameExpense($dateCurrentYear);
        $query_name_incomes_sum_current_year = \App\Models\ModelPersonalBudget::incomesSum($dateCurrentYear);
        $query_name_expenses_sum_current_year = \App\Models\ModelPersonalBudget::expensesSum($dateCurrentYear);
        $chart_incomes_current_year = \App\Models\ModelPersonalBudget::sumOfNamesFromIncomesToChart($dateCurrentYear);
        $chart_expenses_current_year = \App\Models\ModelPersonalBudget::sumOfNamesFromExpensesToChart($dateCurrentYear);

        View::renderTemplate('PersonalBudget/browseSelectedPeriodCurrentYear.html', [
            'user' => $this->user,
            'date_from_to_current_year' => $date_from_to_current_year,
            'query_name_income_current_year' => $query_name_income_current_year,
            'query_name_expense_current_year' => $query_name_expense_current_year,
            'query_name_incomes_sum_current_year' => $query_name_incomes_sum_current_year,
            'query_name_expenses_sum_current_year' => $query_name_expenses_sum_current_year,
            'chart_incomes_current_year' => $chart_incomes_current_year,
            'chart_expenses_current_year' => $chart_expenses_current_year
        ]);
    }

    public function successSelectedPeriodChooseTheDateAction()
    {
        $start_date_selected_period = \App\Models\ModelPersonalBudget::getStartDateSelectedPeriod(); 
        $end_date_selected_period = \App\Models\ModelPersonalBudget::getEndDateSelectedPeriod(); 

        $query_name_incomes_selected_period = \App\Models\ModelPersonalBudget::getSelectedPeriodQueryNameIncome();
        $query_name_expenses_selected_period = \App\Models\ModelPersonalBudget::getSelectedPeriodQueryNameExpense();
        $query_name_incomes_sum_selected_period = \App\Models\ModelPersonalBudget::incomesSelectedPeriodSum();
        $query_name_expenses_sum_selected_period = \App\Models\ModelPersonalBudget::expensesSelectedPeriodSum();
        $chart_incomes_selected_period = \App\Models\ModelPersonalBudget::sumOfNamesFromIncomesToChartSelectedPeriod();
        $chart_expenses_selected_period = \App\Models\ModelPersonalBudget::sumOfNamesFromExpensesToChartSelectedPeriod();

        View::renderTemplate('PersonalBudget/successSelectedPeriodChooseTheDate.html', [
            'user' => $this->user,
            'start_date_selected_period' => $start_date_selected_period,
            'end_date_selected_period' => $end_date_selected_period,
            'query_name_incomes_selected_period' => $query_name_incomes_selected_period,
            'query_name_expenses_selected_period' => $query_name_expenses_selected_period,
            'query_name_incomes_sum_selected_period' => $query_name_incomes_sum_selected_period,
            'query_name_expenses_sum_selected_period' => $query_name_expenses_sum_selected_period,
            'chart_incomes_selected_period' => $chart_incomes_selected_period,
            'chart_expenses_selected_period' => $chart_expenses_selected_period
        ]);
    }

    public function browseselectedperiodprocessingAction()
    {
        View::renderTemplate('PersonalBudget/browseSelectedPeriodProcessingChooseTheDate.html', [
            'user' => $this->user
        ]);
    }
    

    public function successAddIncomeAction()
    {
        View::renderTemplate('PersonalBudget/successAddIncome.html', [
            'user' => $this->user
        ]);
    }

    public function successAddExpenseAction()
    {
        View::renderTemplate('PersonalBudget/successAddExpense.html', [
            'user' => $this->user
        ]);
    }
    
}
