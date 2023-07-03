<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\User;
use \App\Models\ModelPersonalBudget;
// use \App\Models\User;

/**
 * Signup controller
 *
 * PHP version 7.0
 */
#[\AllowDynamicProperties]
class Signup extends \Core\Controller
{
    public $user;
    // public $userCurrent;

    /**
     * Show the signup page
     *
     * @return void
     */
    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }

    /**
     * Sign up a new user
     *
     * @return void
     */
    public function createAction()
    {
        $user = new User($_POST);

        $personalBudget = new ModelPersonalBudget($_POST);

        if ($user->save()) {
            $emailOfUser = $_POST['email']; 
            // echo "email wynosi: ".$emailOfUser;   
            // exit;

            // $userId = $personalBudget->getRegisteredUser();
            // $userId = $personalBudget->getUserId($emailOfUser);
            $userId = $user->getUserId($emailOfUser);

            // $this->user = Auth::getUser(); 

            // print_r($this->user);
            // exit;

            // $array = get_object_vars($this->user);   

            // echo "Wynosi: ".$array['email'];
            // exit;
            $personalBudget->inserIncomesIntoIncomesCategoryAssignedToUsers($userId);
            $personalBudget->insertExpensesIntoExpensesCategoryAssignedToUsers($userId);
            $personalBudget->insertIntoPaymentMethodsAssignedToUsers($userId);

            

            $user->sendActivationEmail();

            $this->redirect('/signup/success');

        } else {

            View::renderTemplate('Signup/new.html', [
                'user' => $user
            ]);

        } 
    }

    /**
     * Show the signup success page
     *
     * @return void
     */
    public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }

    /**
     * Activate a new account
     *
     * @return void
     */
    public function activateAction()
    {
        User::activate($this->route_params['token']);

        $this->redirect('/signup/activated');
    }

    /**
     * Show the activation success page
     *
     * @return void
     */
    public function activatedAction()
    {
        View::renderTemplate('Signup/activated.html');
    }
}
