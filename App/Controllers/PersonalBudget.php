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

    public function successAreyouSuredeleteFromIncomes()
    {
        View::renderTemplate('PersonalBudget/successAreyouSuredeleteFromIncomes.html');
    }

    public function successAreyouSuredeleteFromExpenses()
    {
        View::renderTemplate('PersonalBudget/successAreyouSuredeleteFromExpenses.html');
    }    

    public function successEditIncomes()
    {
        View::renderTemplate('PersonalBudget/editIncome.html');
    }

    public function successEditExpenses()
    {
        View::renderTemplate('PersonalBudget/editExpense.html');
    }

    // public static function redirectToChosenPeriod(){
    //     if($_SESSION['paymentMethod'] == "currentMonth"){
    //         personalBudget::redirect('/personalbudget/successbrowseselectedperiodcurrentmonth');
    //     } else if ($_SESSION['paymentMethod'] == "currentYear"){         
    //         personalBudget::redirect('/personalbudget/successbrowseselectedperiodcurrentyear');
    //     } else if ($_SESSION['paymentMethod'] == "lastMonth"){         
    //         personalBudget::redirect('/personalbudget/successbrowseselectedperiodlastmonth');
    //     }
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
        // $allFromExpenses = \App\Models\ModelPersonalBudget::selectAllFromExpensesToEdit($_SESSION['idExpensesEditRow']);
        // print_r ($allFromExpenses);

        // foreach($allFromExpenses as $row)
        // {
        //     echo "<br>";
        //     echo "Ilość ".$row['amn'] . "<br>";
        //     echo "Data ".$row['dateExp'] . "<br>";
        //     echo "pay ".$row['pay'] . PHP_EOL;
        // }
        // exit;
        // if(isset($_POST['deleteRow'])) {
        //     $id = $_POST['deleteRow'];
            // echo "Id wynosi: ".$id;
            // exit;
        // }
        if ($personalBudget->updateIncomes()) {
            Flash::addMessage('Pomyślnie zakończono edycję');
            $this->redirectToChosenPeriod();
            // personalBudget::redirectToChosenPeriod();

            // if($_SESSION['paymentMethod'] == "currentMonth"){
            //     $this->redirect('/personalbudget/successbrowseselectedperiodcurrentmonth');
            // } else if ($_SESSION['paymentMethod'] == "currentYear"){         
            //     $this->redirect('/personalbudget/successbrowseselectedperiodcurrentyear');
            // } else if ($_SESSION['paymentMethod'] == "lastMonth"){         
            //     $this->redirect('/personalbudget/successbrowseselectedperiodlastmonth');
            // }
        }
    }

    public function updateExpenseAction()
    {
        $personalBudget = new ModelPersonalBudget($_POST);
        // $allFromExpenses = \App\Models\ModelPersonalBudget::selectAllFromExpensesToEdit($_SESSION['idExpensesEditRow']);
        // print_r ($allFromExpenses);

        // foreach($allFromExpenses as $row)
        // {
        //     echo "<br>";
        //     echo "Ilość ".$row['amn'] . "<br>";
        //     echo "Data ".$row['dateExp'] . "<br>";
        //     echo "pay ".$row['pay'] . PHP_EOL;
        // }
        // exit;
        // if(isset($_POST['deleteRow'])) {
        //     $id = $_POST['deleteRow'];
            // echo "Id wynosi: ".$id;
            // exit;
        // }
        if ($personalBudget->updateExpenses()) {
            Flash::addMessage('Pomyślnie zakończono edycję');
            $this->redirectToChosenPeriod();
            // if($_SESSION['paymentMethod'] == "currentMonth"){
            //     $this->redirect('/personalbudget/successbrowseselectedperiodcurrentmonth');
            // } else if ($_SESSION['paymentMethod'] == "currentYear"){
            //     // Flash::addMessage('Pomyślnie zakończono edycję');
            //     $this->redirect('/personalbudget/successbrowseselectedperiodcurrentyear');
            // } else if ($_SESSION['paymentMethod'] == "lastMonth"){
            //     // Flash::addMessage('Pomyślnie zakończono edycję');
            //     $this->redirect('/personalbudget/successbrowseselectedperiodlastmonth');
            // }
        }
        unset($_SESSION['paymentMethod']);
    }

    public function editIncomes()
    {
        if(isset($_POST['editRowIncomes'])) {
            $_SESSION['idIncomesEditRow'] = $_POST['editRowIncomes'];
        }
        // echo "Edit expenses id wynosi: ".$_SESSION['idExpensesEditRow'];
        // exit;
        $this->redirect('/personalbudget/successeditincomes');
    }

    public function editExpenses()
    {
        if(isset($_POST['editRow'])) {
            $_SESSION['idExpensesEditRow'] = $_POST['editRow'];
        }
        // echo "Edit expenses id wynosi: ".$_SESSION['idExpensesEditRow'];
        // exit;
        $this->redirect('/personalbudget/successeditexpenses');
    }

    public function areYouSureDeleteFromIncomes()
    {
        if(isset($_POST['deleteRowIncomes'])) {
            $_SESSION['idIncomesDelete'] = $_POST['deleteRowIncomes'];

            // echo "Id wynosi: ".$_SESSION['idExpensesDelete'];
            // exit;
        }

        if(isset($_POST['myOrdinalNumberDeleteIncomes'])) {

            $_SESSION['myOrdinalNumberDeleteIncomesVar'] = $_POST['myOrdinalNumberDeleteIncomes'];

            // echo "Id wynosi: ".$_SESSION['idExpensesDelete'];
            // exit;
        }
        $this->redirect('/personalbudget/successareyousuredeletefromincomes');
    }
    
    public function areYouSureDeleteFromExpenses()
    {
        if(isset($_POST['deleteRow'])) {
            $_SESSION['idExpensesDelete'] = $_POST['deleteRow'];

            // echo "Id wynosi: ".$_SESSION['idExpensesDelete'];
            // exit;
        }

        if(isset($_POST['myOrdinalNumberDeleteExpenses'])) {
            
            $_SESSION['myOrdinalNumberDeleteExpensesVar'] = $_POST['myOrdinalNumberDeleteExpenses'];

            // echo "Id wynosi: ".$_SESSION['idExpensesDelete'];
            // exit;
        }

        $this->redirect('/personalbudget/successareyousuredeletefromexpenses');
    }

    public function deleteFromIncomes()
    {
        $personalBudget = new ModelPersonalBudget($_POST);
        // if(isset($_POST['deleteRow'])) {
        //     $id = $_POST['deleteRow'];
            // echo "Id wynosi: ".$id;
            // exit;
        // }
        if ($personalBudget->deleteIncome()) {
            //  echo "SESSION PAYMENT METHOD: ".$_SESSION['paymentMethod'];
            // exit;
            Flash::addMessage('Pomyślnie usunięto rekord');
            $this->redirectToChosenPeriod();
            // if ($_SESSION['paymentMethod'] == "currentMonth"){
            //     $this->redirect('/personalbudget/successbrowseselectedperiodcurrentmonth');
            // } elseif ($_SESSION['paymentMethod'] == "currentYear"){           
            //     $this->redirect('/personalbudget/successbrowseselectedperiodcurrentyear');
            // } else if ($_SESSION['paymentMethod'] == "lastMonth"){         
            //     $this->redirect('/personalbudget/successbrowseselectedperiodlastmonth');
            // }
            // unset($_SESSION['paymentMethod']);
        }
    }

    public function deleteFromExpenses()
    {
        $personalBudget = new ModelPersonalBudget($_POST);
        // if(isset($_POST['deleteRow'])) {
        //     $id = $_POST['deleteRow'];
            // echo "Id wynosi: ".$id;
            // exit;
        // }
        if ($personalBudget->deleteExpense()) {
            //  echo "SESSION PAYMENT METHOD: ".$_SESSION['paymentMethod'];
            // exit;
            Flash::addMessage('Pomyślnie usunięto rekord');
            $this->redirectToChosenPeriod();
            // if ($_SESSION['paymentMethod'] == "currentMonth"){
            //     $this->redirect('/personalbudget/successbrowseselectedperiodcurrentmonth');
            // } elseif ($_SESSION['paymentMethod'] == "currentYear"){
            //     $this->redirect('/personalbudget/successbrowseselectedperiodcurrentyear');
            // } else if ($_SESSION['paymentMethod'] == "lastMonth"){         
            //     $this->redirect('/personalbudget/successbrowseselectedperiodlastmonth');
            // }
            // unset($_SESSION['paymentMethod']);
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
        // $wartosc = false;
        // $userValue = Auth::getUser();  
        // $array = get_object_vars($userValue);
        // $user_object = new User($_POST);
        // $userId = $user_object->getUserId($array['email']);
        $paymentMethod = $_POST['paymentMethod'];
        $_SESSION['paymentMethod'] = $paymentMethod;

        // echo "Payment method wynosi:".$_SESSION['paymentMethod'];
        // exit;

        // echo "SESSION PAYMENT METHOD: ".$_SESSION['paymentMethod'];
        // exit;


        // if(isset($_SESSION['whichPeriod'])){
        //     unset($_SESSION['whichPeriod']);
        // }
        // if(isset($_SESSION['currentMonth'])){
        //     unset($_SESSION['currentMonth']);
        // }
        // if(isset($_SESSION['lastMonth'])){
        //     unset($_SESSION['lastMonth']);
        // }
        // if(isset($_SESSION['currentYear'])){
        //     unset($_SESSION['currentYear']);
        // }



        if($paymentMethod=='currentMonth')
            {
                // $_SESSION['currentMonth'] = "currentMonth";
                // $_SESSION['whichPeriod'] = "currentMonth";

                $this->redirect('/personalbudget/successbrowseselectedperiodcurrentmonth');
            }


        elseif($paymentMethod=='lastMonth'){
            {
                // $_SESSION['lastMonth'] = "lastMonth";
                // $_SESSION['whichPeriod'] = "lastMonth";
                $this->redirect('/personalbudget/successbrowseselectedperiodlastmonth');
            }
        }

        elseif ($paymentMethod=='currentYear'){
            {
                // $_SESSION['currentYear'] = "currentYear";
                // $_SESSION['whichPeriod'] = "currentYear";
                $this->redirect('/personalbudget/successbrowseselectedperiodcurrentyear');      
            }
        }
        elseif ($paymentMethod=='selectedPeriod'){
            // $this->redirect('/personalbudget/browseselectedperiodprocessing');
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

    public function choosecorrectdate()
    {
        View::renderTemplate('PersonalBudget/chooseCorrectDate.html');
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
