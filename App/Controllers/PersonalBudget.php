<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\ModelPersonalBudget;
use \App\Models\User;



use \App\Auth;
use \App\Flash;

#[\AllowDynamicProperties]
class personalBudget extends \Core\Controller
{
    public $user;

    public function addIncomeAction()
    {
        View::renderTemplate('PersonalBudget/addIncome.html');
    }

    public function addExpenseAction()
    {
        View::renderTemplate('PersonalBudget/addExpense.html');
    } 

    public function browseTheBalance()
    {
        View::renderTemplate('PersonalBudget/browseTheBalance.html');
    } 

    public function successDeletedExpenseAction()
    {
        View::renderTemplate('PersonalBudget/successDeletedExpense.html');
    }

    public function successAreyouSuredeleteFromExpenses()
    {
        View::renderTemplate('PersonalBudget/successAreyouSuredeleteFromExpenses.html');
    }    

    public function areYouSureDeleteFromExpenses()
    {
        if(isset($_POST['deleteRow'])) {
            $_SESSION['idExpensesDelete'] = $_POST['deleteRow'];

            // echo "Id wynosi: ".$_SESSION['idExpensesDelete'];
            // exit;
        }
        $this->redirect('/personalbudget/successareyousuredeletefromexpenses');
    }

    public function deleteFromExpenses()
    {
        $personalBudget = new ModelPersonalBudget($_POST);
        // if(isset($_POST['deleteRow'])) {
        //     $id = $_POST['deleteRow'];
            // echo "Id wynosi: ".$id;
            // exit;
        // }
        if ($personalBudget->deleteExpense($_SESSION['idExpensesDelete'])) {
            $this->redirect('/personalbudget/successbrowseselectedperiodcurrentyear');
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

        return $dateFdateSelectedPeriod1romTo;
    }

    public static function dateFromToSelectedPeriodDate2()
    {
        $dateSelectedPeriod2 = $_POST['dateSelectedPeriod2'];

        return $dateSelectedPeriod2;
    } 

    public function newBrowseTheBalanceAction()
    {
        $wartosc = false;
        $userValue = Auth::getUser();  
        $array = get_object_vars($userValue);
        $user_object = new User($_POST);
        $userId = $user_object->getUserId($array['email']);
        $paymentMethod = $_POST['paymentMethod'];

        if($paymentMethod=='currentMonth')
            {
                $_SESSION['currentMonth'] = "currentMonth";
                $_SESSION['whichPeriod'] = "currentMonth";

                $this->redirect('/personalbudget/successbrowseselectedperiodcurrentmonth');
            }


        elseif($paymentMethod=='lastMonth'){
            {
                $_SESSION['lastMonth'] = "lastMonth";
                $_SESSION['whichPeriod'] = "lastMonth";
                $this->redirect('/personalbudget/successbrowseselectedperiodlastmonth');
            }
        }

        elseif ($paymentMethod=='currentYear'){
            {
                $_SESSION['currentYear'] = "currentYear";
                $_SESSION['whichPeriod'] = "currentYear";
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

        $this->redirect('/personalbudget/successselectedperiodchoosethedate');        
    }

    public function successBrowseSelectedPeriodCurrentMonth()
    {
        View::renderTemplate('PersonalBudget/browseSelectedPeriodCurrentMonth.html');
    }

    public function successBrowseSelectedPeriodLastMonth()
    {
        View::renderTemplate('PersonalBudget/browseSelectedPeriodLastMonth.html');
    }

    public function successBrowseSelectedPeriodCurrentYear()
    {
        View::renderTemplate('PersonalBudget/browseSelectedPeriodCurrentYear.html');
    }

    public function successSelectedPeriodChooseTheDate()
    {
        View::renderTemplate('PersonalBudget/successSelectedPeriodChooseTheDate.html');
    }

    public function browseselectedperiodprocessing()
    {
        View::renderTemplate('PersonalBudget/browseSelectedPeriodProcessingChooseTheDate.html');
    }
    

    public function successAddIncomeAction()
    {
        View::renderTemplate('PersonalBudget/successAddIncome.html');
    }

    public function successAddExpenseAction()
    {
        View::renderTemplate('PersonalBudget/successAddExpense.html');
    }
    
}
