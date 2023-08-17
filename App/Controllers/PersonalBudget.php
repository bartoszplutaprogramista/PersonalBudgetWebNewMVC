<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\ModelPersonalBudget;
use \App\Models\User;



use \App\Auth;
use \App\Flash;

/**
 * Items controller (example)
 *
 * PHP version 7.0
 */
//class Items extends \Core\Controller
#[\AllowDynamicProperties]

class personalBudget extends \Core\Controller
{
    public $user;

    /**
     * Require the user to be authenticated before giving access to all methods in the controller
     *
     * @return void
     */
    /*
    protected function before()
    {
        $this->requireLogin();
    }
    */

    /**
     * Items index
     *
     * @return void
     */
    // protected function before()
    // {
    //     // parent::before();

    //     $this->user = Auth::getUser();
    // }

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

    public function newIncomeAction()
    {
    //     echo "Wartość email: ";
    //     print_r ($this->user);

    //   //  $array = get_object_vars($this->user);

    // //    echo "Wartość email moja : ".$array['email'];
    //     // print_r($this->user, email);
    //     exit;

        $this->user = Auth::getUser();  

    // echo "Wartość email: ";
    // print_r ($this->user);
    // exit;

        $personalBudget = new ModelPersonalBudget($_POST);
        // $personalBudget = new ModelPersonalBudget($_POST);
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

    public function newBrowseTheBalanceAction()
    {
        $userValue = Auth::getUser();  
        $array = get_object_vars($userValue);
        $user_object = new User($_POST);
        $userId = $user_object->getUserId($array['email']);

        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->getQueryNameIncome($userId)) {
            $this->redirect('/personalbudget/successbrowseselectedperiod');      
        } 

        // $array = get_object_vars($this->user);
        // $user_object = new User($_POST);
        // $userId = $user_object->getUserId($array['email']);

        // echo "USER ID WYNOSI: ". $userId;


        // $paymentMethod = $_POST['paymentMethod'];
        
        // if ($paymentMethod=='currentYear'){
        //     if (\App\Models\ModelPersonalBudget::getQueryNameIncome($userId)) {
        //         $this->redirect('/personalbudget/successbrowseselectedperiod');      
        //     }       
        // } 
    }  

    public function successBrowseSelectedPeriod()
    {
        View::renderTemplate('PersonalBudget/browseSelectedPeriod.html');
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
