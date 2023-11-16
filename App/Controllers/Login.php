<?php

namespace App\Controllers;

use \Core\View;
use \App\Models\User;
use \App\Auth;
use \App\Flash;

class Login extends \Core\Controller
{
    public function newAction()
    {
        View::renderTemplate('Login/new.html');
    }

    public function createAction()
    {
        $user = User::authenticate($_POST['email'], $_POST['password']);
        
        $remember_me = isset($_POST['remember_me']);

        if ($user) {

            Auth::login($user, $remember_me);

            Flash::addMessage('Zalogowano poprawnie');

            $this->redirect(Auth::getReturnToPage());

        } else {

            Flash::addMessage('Nie udało się zalogować, spróbuj ponownie', Flash::WARNING);

            View::renderTemplate('Login/new.html', [
                'email' => $_POST['email'],
                'remember_me' => $remember_me
            ]);
        }
    }

    public function destroyAction()
    {
        // if(isset($_SESSION['paymentMethod'])){
        //     unset($_SESSION['paymentMethod']);
        //     if(empty($_SESSION['paymentMethod'])){
        //         echo "SESSION['paymentMethod'] is unset now";
        //     }
        // }
        // if(isset($_SESSION['idIncomesEditRow'])){
        //     unset($_SESSION['idIncomesEditRow']);
        //     echo "SESSION['idIncomesEditRow'] is unset now";
        // }
        // if(isset($_SESSION['idExpensesEditRow'])){
        //     unset($_SESSION['idExpensesEditRow']);
        //     echo "SESSION['idExpensesEditRow'] is unset now";
        // }
        // if(isset($_SESSION['idIncomesDelete'])){
        //     unset($_SESSION['idIncomesDelete']);
        //     echo "SESSION['idIncomesDelete'] is unset now";
        // }
        // if(isset($_SESSION['myOrdinalNumberDeleteIncomesVar'])){
        //     unset($_SESSION['myOrdinalNumberDeleteIncomesVar']);
        //     echo "SESSION['myOrdinalNumberDeleteIncomesVar'] is unset now";
        // }
        // if(isset($_SESSION['idExpensesDelete'])){
        //     unset($_SESSION['idExpensesDelete']);
        //     echo "SESSION['idExpensesDelete'] is unset now";
        // }
        // if(isset($_SESSION['myOrdinalNumberDeleteExpensesVar'])){
        //     unset($_SESSION['myOrdinalNumberDeleteExpensesVar']);
        //     echo "SESSION['myOrdinalNumberDeleteExpensesVar'] is unset now";
        // }
        // if(isset($_SESSION['start_date'])){
        //     unset($_SESSION['start_date']);
        //     echo "SESSION['start_date'] is unset now";
        // }
        // if(isset($_SESSION['end_date'])){
        //     unset($_SESSION['end_date']);
        //     echo "SESSION['end_date'] is unset now";
        // }
        // if(isset($_SESSION['userIdSession'])){
        //     unset($_SESSION['userIdSession']);
        //     if(empty($_SESSION['userIdSession'])){
        //         echo "SESSION['userIdSession'] is unset now";
        //     }
        // }

        // session_destroy();

        Auth::logout();
        // if(empty($_SESSION['paymentMethod'])){
        //     echo "SESSION['paymentMethod'] is unset now";
        // }
        // if(empty($_SESSION['idIncomesEditRow'])){
        //     echo "SESSION['idIncomesEditRow'] is unset now";
        // }
        // if(empty($_SESSION['idExpensesEditRow'])){
        //     echo "SESSION['idExpensesEditRow'] is unset now";
        // }
        // if(empty($_SESSION['idIncomesDelete'])){
        //     echo "SESSION['idIncomesDelete'] is unset now";
        // }
        // if(empty($_SESSION['myOrdinalNumberDeleteIncomesVar'])){
        //     echo "SESSION['myOrdinalNumberDeleteIncomesVar'] is unset now";
        // }
        // if(empty($_SESSION['myOrdinalNumberDeleteExpensesVar'])){
        //     echo "SESSION['myOrdinalNumberDeleteExpensesVar'] is unset now";
        // }
        // if(empty($_SESSION['start_date'])){
        //     echo "SESSION['start_date'] is unset now";
        // }
        // if(empty($_SESSION['end_date'])){
        //     echo "SESSION['end_date'] is unset now";
        // }
        // if(empty($_SESSION['userIdSession'])){
        //     echo "SESSION['userIdSession'] is unset now";
        // }
        // exit;

        $this->redirect('/login/show-logout-message');
        
    }

    public function showLogoutMessageAction()
    {
        Flash::addMessage('Wylogowano poprawnie');

        $this->redirect('/');
    }
}
