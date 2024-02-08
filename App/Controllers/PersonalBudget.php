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
        View::renderTemplate('PersonalBudget/addIncome.html', [
            'user' => $this->user
        ]);
    }

    public function addExpenseAction()
    {
        View::renderTemplate('PersonalBudget/addExpense.html', [
            'user' => $this->user
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
        View::renderTemplate('PersonalBudget/successAreYouSureDeleteFromIncomes.html', [
            'user' => $this->user
        ]);
    }

    public function successAreyouSuredeleteFromExpensesAction()
    {
        View::renderTemplate('PersonalBudget/successAreYouSureDeleteFromExpenses.html', [
            'user' => $this->user
        ]);
    }    

    public function successEditIncomesAction()
    {
        View::renderTemplate('PersonalBudget/editIncome.html', [
            'user' => $this->user
        ]);
    }

    public function successEditExpensesAction()
    {
        View::renderTemplate('PersonalBudget/editExpense.html', [
            'user' => $this->user
        ]);
    }

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

    public function editIncomes()
    {
        if(isset($_POST['editRowIncomes'])) {
            $_SESSION['idIncomesEditRow'] = $_POST['editRowIncomes'];
        }
        $this->redirect('/personalbudget/successeditincomes');
    }

    public function editExpenses()
    {
        if(isset($_POST['editRow'])) {
            $_SESSION['idExpensesEditRow'] = $_POST['editRow'];
        }
        $this->redirect('/personalbudget/successeditexpenses');
    }

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

    public function choosecorrectdateAction()
    {
        View::renderTemplate('PersonalBudget/chooseCorrectDate.html', [
            'user' => $this->user
        ]);
    }

    public function successBrowseSelectedPeriodCurrentMonthAction()
    {
        View::renderTemplate('PersonalBudget/browseSelectedPeriodCurrentMonth.html', [
            'user' => $this->user
        ]);
    }

    public function successBrowseSelectedPeriodLastMonthAction()
    {
        View::renderTemplate('PersonalBudget/browseSelectedPeriodLastMonth.html', [
            'user' => $this->user
        ]);
    }

    public function successBrowseSelectedPeriodCurrentYearAction()
    {
        View::renderTemplate('PersonalBudget/browseSelectedPeriodCurrentYear.html', [
            'user' => $this->user
        ]);
    }

    public function successSelectedPeriodChooseTheDateAction()
    {
        View::renderTemplate('PersonalBudget/successSelectedPeriodChooseTheDate.html', [
            'user' => $this->user
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

    public function editIncomesCategory()
    {
        $editIncomesCateegoryID = $_POST['editIncomesCat'];
        $_SESSION['incomesCatID'] = $editIncomesCateegoryID;

        View::renderTemplate('PersonalBudget/editIncomesCategory.html', [
            'user' => $this->user
        ]);
    }

    public function changeIncomeNameAction()
    {
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->editIncomesCategory()) {
            $this->redirect('/profile/categoryconfigurator');      
        }
    }
    
}
