<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\ModelPersonalBudget;

#[\AllowDynamicProperties]
class Profile extends Authenticated
{
    public $user;

    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

    public function showAction()
    {
        View::renderTemplate('Profile/show.html', [
            'user' => $this->user
        ]);
    }

    public function editAction()
    {
        View::renderTemplate('Profile/edit.html', [
            'user' => $this->user
        ]);
    }

    public function deleteAccount()
    {
        View::renderTemplate('Profile/areYouSureDeleteAccount.html', [
            'user' => $this->user
        ]);
    }

    public function categoryConfiguratorAction()
    {
        View::renderTemplate('Profile/categoryConfigurator.html', [
            'user' => $this->user
        ]);
    }

    public function updateAction()
    {
        if ($this->user->updateProfile($_POST)) {

            Flash::addMessage('Zmiany zapisane');

            $this->redirect('/profile/show');

        } else {

            View::renderTemplate('Profile/edit.html', [
                'user' => $this->user
            ]);

        }
    }
    
    public function editIncomesCategory()
    {
        $editIncomesCategoryID = $_POST['editIncomesCat'];
        $_SESSION['incomesCatID'] = $editIncomesCategoryID;

        View::renderTemplate('Profile/editIncomesCategory.html', [
            'user' => $this->user
        ]);
    }

    public function editExpensesCategory()
    {
        $editExpensesCategoryID = $_POST['editExpensesCat'];
        $_SESSION['expensesCatID'] = $editExpensesCategoryID;

        View::renderTemplate('Profile/editExpensesCategory.html', [
            'user' => $this->user
        ]);
    }

    public function editPaymentMethodCategory()
    {
        $editPaymentMethCategoryID = $_POST['editPaymentMethodCat'];

        $_SESSION['payMethCatID'] = $editPaymentMethCategoryID;

        View::renderTemplate('Profile/editPayMethCategory.html', [
            'user' => $this->user
        ]);
    }

    public function changeIncomeNameAction()
    {
        $editIncomeCategoryName = $_POST['editIncomeCategoryName'];
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->editIncomesCategory($editIncomeCategoryName)) {
            if(isset($_SESSION['incomesCatID'])) {
                unset($_SESSION['incomesCatID']);
            }
            Flash::addMessage('Zmiany zapisane');
            $this->redirect('/profile/categoryconfigurator');      
        }
    }

    public function changeExpenseNameAction()
    {
        $editExpenseCategoryName = $_POST['editExpenseCategoryName'];
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->editExpensesCategory($editExpenseCategoryName)) {
            if(isset($_SESSION['expensesCatID'])) {
                unset($_SESSION['expensesCatID']);
            }
            Flash::addMessage('Zmiany zapisane');
            $this->redirect('/profile/categoryconfigurator');      
        }
    }

    public function changePayMethNameAction()
    {
        $editPayMethCategoryName = $_POST['editPayMethCategoryName'];
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->editPayMethCategory($editPayMethCategoryName)) {
            if(isset($_SESSION['payMethCatID'])) {
                unset($_SESSION['payMethCatID']);
            }
            Flash::addMessage('Zmiany zapisane');
            $this->redirect('/profile/categoryconfigurator');      
        }
    }

    public function deleteIncomeCategoryDataBaseAction()
    {
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->deleteIncomesCategory()&&($personalBudget->deleteIncomesRowRelatedToIncomesCatAssignedToUserId())) {
            if(isset($_SESSION['idIncomesDeleteCat'])) {
                unset($_SESSION['idIncomesDeleteCat']);
            }
            Flash::addMessage('Pomyślnie usunięto kategorię oraz powiązane z nią przychody');
            $this->redirect('/profile/categoryconfigurator');      
        }       
    }

    public function deleteExpenseCategoryDataBaseAction()
    {
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->deleteExpensesCategory()&&($personalBudget->deleteExpensesRowRelatedToExpensesCatAssignedToUserId())) {
            if(isset($_SESSION['idExpensesDeleteCat'])) {
                unset($_SESSION['idExpensesDeleteCat']);
            }
            Flash::addMessage('Pomyślnie usunięto kategorię oraz powiązane z nią wydatki');
            $this->redirect('/profile/categoryconfigurator');      
        }       
    }

    public function deletePaymentMethodsCategoryDataBaseAction()
    {
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->deletePayMethCategory()&&($personalBudget->deleteExpensesRowRelatedToPayMethCatAssignedToUserId())) {
            if(isset($_SESSION['idPayMethDeleteCat'])) {
                unset($_SESSION['idPayMethDeleteCat']);
            }
            Flash::addMessage('Pomyślnie usunięto kategorię oraz powiązane z nią wydatki');
            $this->redirect('/profile/categoryconfigurator');      
        }       
    }

    public function deleteIncomesCategory()
    {
        if(isset($_POST['deleteIncomesCatID'])) {
            $_SESSION['idIncomesDeleteCat'] = $_POST['deleteIncomesCatID'];
        }
        View::renderTemplate('Profile/areYouSureDeleteIncomesCategory.html', [
            'user' => $this->user
        ]);
    }

    public function deleteExpensesCategory()
    {
        if(isset($_POST['deleteExpensesCatID'])) {
            $_SESSION['idExpensesDeleteCat'] = $_POST['deleteExpensesCatID'];

        }
        View::renderTemplate('Profile/areYouSureDeleteExpensesCategory.html', [
            'user' => $this->user
        ]);
    }

    public function deletePaymentMethodsCategory()
    {
        if(isset($_POST['deletePayMethCatID'])) {
            $_SESSION['idPayMethDeleteCat'] = $_POST['deletePayMethCatID'];

        }
        View::renderTemplate('Profile/areYouSureDeletePayMethCategory.html', [
            'user' => $this->user
        ]);
    }

    public function addNewIncomesCategory()
    {
        View::renderTemplate('Profile/addNewIncomesCategory.html', [
            'user' => $this->user
        ]);
    }

    public function addToDataBaseNewIncomesCategory()
    {
        $newIncomeCat = $_POST['addedNewIncomeCat'];
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->addNewIncomesCategory($newIncomeCat)) {
            Flash::addMessage('Dodano nową kategorię');
            $this->redirect('/profile/categoryconfigurator');      
        }
    }

    public function addNewExpensesCategory()
    {
        View::renderTemplate('Profile/addNewExpensesCategory.html', [
            'user' => $this->user
        ]);
    }

    public function addToDataBaseNewExpensesCategory()
    {
        $newExpenseCat = $_POST['addedNewExpenseCat'];
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->addNewExpensesCategory($newExpenseCat)) {
            Flash::addMessage('Dodano nową kategorię');
            $this->redirect('/profile/categoryconfigurator');      
        }
    }

    public function addNewPayMethCategory()
    {
        View::renderTemplate('Profile/addNewPayMethCategory.html', [
            'user' => $this->user
        ]);
    }

    public function addToDataBaseNewPayMethCategory()
    {
        $newPayMethCat = $_POST['addedNewPayMethCat'];
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->addNewPayMethCategory($newPayMethCat)) {
            Flash::addMessage('Dodano nową kategorię');
            $this->redirect('/profile/categoryconfigurator');      
        }
    }

    public function deleteDataBaseAccount()
    {
        $userID = $_SESSION['userIdSession'];
        Auth::logout();
        $personalBudget = new ModelPersonalBudget($_POST);
        if (($personalBudget->deleteFromDataBaseIncomesUserID($userID))&&($personalBudget->deleteFromDataBaseExpensesUserID($userID))&&($personalBudget->deleteFromDataBaseIncomesCategoryAssignedToUser($userID))&&($personalBudget->deleteFromDataBaseExpensesCategoryAssignedToUser($userID))&&($personalBudget->deleteFromDataBasePaymentMethodsCategoryAssignedToUser($userID))&&($personalBudget->deleteFromDataBaseUser($userID))) {          
            $this->redirect('/login/show-message-after-deleting-user-data');
        }
    }
}
