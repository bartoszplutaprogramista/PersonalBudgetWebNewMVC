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
        if(isset($_POST['recaptchaResponse'])){
            $secretKey = "";

            $check = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['recaptchaResponse']);

            $answer = json_decode($check);   
            
            if($answer->success==false){
                echo "POTWIERDŹ ŻE NIE JESTEŚ BOTEM";
            }
        }

        View::renderTemplate('Signup/new.html');
    }

    public function createAction()
    {
        if(isset($_POST['recaptchaResponse'])){
            $secretKey = "";

            $check = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['recaptchaResponse']);

            $answer = json_decode($check);   
            
            if($answer->success==false){
                echo "POTWIERDŹ ŻE NIE JESTEŚ BOTEM";
            }
        }

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
