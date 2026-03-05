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
        $incomes_options_form = \App\Models\ModelPersonalBudget::selectOptionsForIncomes();
        $expenses_options_form_category = \App\Models\ModelPersonalBudget::selectOptionsForExpensesCategory();           
        $expenses_options_form_payment_method = \App\Models\ModelPersonalBudget::selectOptionsForExpensesPaymentMethod(); 

        View::renderTemplate('Profile/categoryConfigurator.html', [
            'user' => $this->user,
            'incomes_options_form' => $incomes_options_form,
            'expenses_options_form_category' => $expenses_options_form_category,
            'expenses_options_form_payment_method' => $expenses_options_form_payment_method
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

        $name_income_category_to_edit = \App\Models\ModelPersonalBudget::selectNameFromIncomesCategoryToEdit(); 

        View::renderTemplate('Profile/editIncomesCategory.html', [
            'user' => $this->user,
            'name_income_category_to_edit' => $name_income_category_to_edit
        ]);
    }

    public function editExpensesCategory()
    {
        $editExpensesCategoryID = $_POST['editExpensesCat'];
        $_SESSION['expensesCatID'] = $editExpensesCategoryID;

        $name_expense_category_to_edit = \App\Models\ModelPersonalBudget::selectNameFromExpensesCategoryToEdit();

        View::renderTemplate('Profile/editExpensesCategory.html', [
            'user' => $this->user,
            'name_expense_category_to_edit' => $name_expense_category_to_edit
        ]);
    }

    public function editPaymentMethodCategory()
    {
        $editPaymentMethCategoryID = $_POST['editPaymentMethodCat'];

        $_SESSION['payMethCatID'] = $editPaymentMethCategoryID;

        $name_pay_meth_category_to_edit = \App\Models\ModelPersonalBudget::selectNameFromPayMethCategoryToEdit();

        View::renderTemplate('Profile/editPayMethCategory.html', [
            'user' => $this->user,
            'name_pay_meth_category_to_edit' => $name_pay_meth_category_to_edit
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

    public function setLimitOfExpenseAction()
    {
        $setLimitValue = $_POST['limitValue'];
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->setLimitValueDB($setLimitValue)) {
            if(isset($_SESSION['idExpenseLimit'])) {
                unset($_SESSION['idExpenseLimit']);
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

        $name_income_category_to_delete = \App\Models\ModelPersonalBudget::selectNameFromIncomesCategoryToDelete();

        View::renderTemplate('Profile/areYouSureDeleteIncomesCategory.html', [
            'user' => $this->user,
            'name_income_category_to_delete' => $name_income_category_to_delete
        ]);
    }

    public function deleteExpensesCategory()
    {
        if(isset($_POST['deleteExpensesCatID'])) {
            $_SESSION['idExpensesDeleteCat'] = $_POST['deleteExpensesCatID'];
        }

        $name_expense_category_to_delete = \App\Models\ModelPersonalBudget::selectNameFromExpensesCategoryToDelete();

        View::renderTemplate('Profile/areYouSureDeleteExpensesCategory.html', [
            'user' => $this->user,
            'name_expense_category_to_delete' => $name_expense_category_to_delete
        ]);
    }

    public function setLimitForExpense()
    {
        if(isset($_POST['setExpenseLimit'])) {
            $_SESSION['idExpenseLimit'] = $_POST['setExpenseLimit'];
        }

        $set_limit_expense = \App\Models\ModelPersonalBudget::selectNameFromExpensesCategoryToLimit();
        $limit_value = \App\Models\ModelPersonalBudget::selectValueOfLimit();

        View::renderTemplate('Profile/setLimit.html', [
            'user' => $this->user,
            'set_limit_expense' => $set_limit_expense,
            'limit_value' => $limit_value
        ]);
    }

    public function deletePaymentMethodsCategory()
    {
        if(isset($_POST['deletePayMethCatID'])) {
            $_SESSION['idPayMethDeleteCat'] = $_POST['deletePayMethCatID'];
        }

        $name_pay_meth_category_to_delete = \App\Models\ModelPersonalBudget::selectNameFromPayMethCategoryToDelete();

        View::renderTemplate('Profile/areYouSureDeletePayMethCategory.html', [
            'user' => $this->user,
            'name_pay_meth_category_to_delete' => $name_pay_meth_category_to_delete
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
