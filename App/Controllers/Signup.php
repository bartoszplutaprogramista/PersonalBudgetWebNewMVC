<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\User;
use \App\Models\ModelPersonalBudget;

#[\AllowDynamicProperties]
class Signup extends \Core\Controller
{
    public $user;

    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }

    public function createAction()
    {
        if(isset($_POST['g-recaptcha-response'])){
            $secretKey = "";

            $check = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['g-recaptcha-response']);

            $answer = json_decode($check);   
            
            if($answer->success==false){
                Flash::addMessage('Potwierdź że nie jesteś botem');
                $this->redirect('/signup/new');
            } else {
                $user = new User($_POST);

                $personalBudget = new ModelPersonalBudget($_POST);
        
                if ($user->save()) {
                    $emailOfUser = $_POST['email']; 
                    $userId = $user->getUserId($emailOfUser);
        
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
        } else {
            Flash::addMessage('Potwierdź że nie jesteś botem');
            $this->redirect('/signup/new');
        }
    }
    public function successAction()
    {
        View::renderTemplate('Signup/success.html');
    }

    public function activateAction()
    {
        User::activate($this->route_params['token']);

        $this->redirect('/signup/activated');
    }

    public function activatedAction()
    {
        View::renderTemplate('Signup/activated.html');
    }
}
