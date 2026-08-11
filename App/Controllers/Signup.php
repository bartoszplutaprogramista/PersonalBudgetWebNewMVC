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
    // public $errors = [];
    // private $data;

    // public function __construct($data = [])
    // {
    //     $this->data = $data;
    // }

    public function newAction()
    {
        View::renderTemplate('Signup/new.html');
    }

    // public function validateSignup()
    // {
    //     // Walidacja imienia
    //     $name = trim($this->data['name'] ?? '');
    //     if ($name === '' || mb_strlen($name) > 50) {
    //         $this->errors[] = 'Imię jest wymagane i nie może przekraczać 50 znaków';
    //     }

    //     // Walidacja email
    //     $email = filter_var($this->data['email'] ?? null, FILTER_VALIDATE_EMAIL);
    //     if ($email === false) {
    //         $this->errors[] = 'Nieprawidłowy adres e-mail';
    //     }

    //     // Walidacja hasła
    //     $password = $this->data['password'] ?? '';

    //     if ($password === '') {
    //         $this->errors[] = 'Hasło jest wymagane';
    //     } elseif (strlen($password) < 6) {
    //         $this->errors[] = 'Hasło musi mieć co najmniej 6 znaków';
    //     } elseif (!preg_match('/[A-Za-z]/', $password)) {
    //         $this->errors[] = 'Hasło musi zawierać przynajmniej jedną literę';
    //     } elseif (!preg_match('/[0-9]/', $password)) {
    //         $this->errors[] = 'Hasło musi zawierać przynajmniej jedną cyfrę';
    //     }

    //     return empty($this->errors);
    // }


public function createAction()
    {
        // if (!$this->validateSignup()) {
        //     return $this->errors;
        // }



        $user = new User($_POST);

                $personalBudget = new ModelPersonalBudget($_POST);
        
                if ($user->save()) {
                    $emailOfUser = $_POST['email']; 
                    $userId = $user->getUserId($emailOfUser);
        
                    $personalBudget->insertIncomesIntoIncomesCategoryAssignedToUsers($userId);
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


    // public function createAction()
    // {
    //     if(isset($_POST['g-recaptcha-response'])){
    //         $secretKey = "";

    //         $check = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secretKey.'&response='.$_POST['g-recaptcha-response']);

    //         $answer = json_decode($check);   
            
    //         if($answer->success==false){
    //             Flash::addMessage('Potwierdź że nie jesteś botem');
    //             $this->redirect('/signup/new');
    //         } else {
    //             $user = new User($_POST);

    //             $personalBudget = new ModelPersonalBudget($_POST);
        
    //             if ($user->save()) {
    //                 $emailOfUser = $_POST['email']; 
    //                 $userId = $user->getUserId($emailOfUser);
        
    //                 $personalBudget->insertIncomesIntoIncomesCategoryAssignedToUsers($userId);
    //                 $personalBudget->insertExpensesIntoExpensesCategoryAssignedToUsers($userId);
    //                 $personalBudget->insertIntoPaymentMethodsAssignedToUsers($userId);
                    
    //                 $user->sendActivationEmail();
        
    //                 $this->redirect('/signup/success');
        
    //             } else {
        
    //                 View::renderTemplate('Signup/new.html', [
    //                     'user' => $user
    //                 ]);
        
    //             } 
    //         }
    //     } else {
    //         Flash::addMessage('Potwierdź że nie jesteś botem');
    //         $this->redirect('/signup/new');
    //     }
    // }
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
