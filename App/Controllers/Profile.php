<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\ModelPersonalBudget;
use \App\Csrf;

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
            'user' => $this->user,
            'csrf_token' => Csrf::generate()
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
            'expenses_options_form_payment_method' => $expenses_options_form_payment_method,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function updateAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        if ($this->user->updateProfile($_POST)) {

            Flash::addMessage('Zmiany zapisane');

            $this->redirect('/profile/show');

        } else {

            View::renderTemplate('Profile/edit.html', [
                'user' => $this->user,
                'csrf_token' => Csrf::generate()
            ]);

        }
    }
    
    public function editIncomesCategory()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $editIncomesCategoryID = $_POST['editIncomesCat'];
        // $_SESSION['incomesCatID'] = $editIncomesCategoryID;

        $name_income_category_to_edit = \App\Models\ModelPersonalBudget::selectNameFromIncomesCategoryToEdit($editIncomesCategoryID); 

        View::renderTemplate('Profile/editIncomesCategory.html', [
            'user' => $this->user,
            'name_income_category_to_edit' => $name_income_category_to_edit,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function editExpensesCategory()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $editExpensesCategoryID = $_POST['editExpensesCat'];
        // $_SESSION['expensesCatID'] = $editExpensesCategoryID;

        $name_expense_category_to_edit = \App\Models\ModelPersonalBudget::selectNameFromExpensesCategoryToEdit($editExpensesCategoryID);

        View::renderTemplate('Profile/editExpensesCategory.html', [
            'user' => $this->user,
            'name_expense_category_to_edit' => $name_expense_category_to_edit,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function editPaymentMethodCategory()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $editPaymentMethCategoryID = $_POST['editPaymentMethodCat'];

        // $_SESSION['payMethCatID'] = $editPaymentMethCategoryID;

        $name_pay_meth_category_to_edit = \App\Models\ModelPersonalBudget::selectNameFromPayMethCategoryToEdit($editPaymentMethCategoryID);

        View::renderTemplate('Profile/editPayMethCategory.html', [
            'user' => $this->user,
            'name_pay_meth_category_to_edit' => $name_pay_meth_category_to_edit,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function changeIncomeNameAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $editIncomeCategoryName = $_POST['editIncomeCategoryName'];
        if (!$editIncomeCategoryName) {
            ($this->redirect('/profile/categoryconfigurator'));
        }
        $editIncomeCategoryID = filter_input(INPUT_POST, 'incomeCategoryEditedID', FILTER_VALIDATE_INT);
        if (!$editIncomeCategoryID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }
        // $editIncomeCategoryID = $_POST['incomeCategoryEditedID'];
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->editIncomesCategory($editIncomeCategoryName, $editIncomeCategoryID)) {
            if(isset($_SESSION['incomesCatID'])) {
                unset($_SESSION['incomesCatID']);
            }
            Flash::addMessage('Zmiany zapisane');
            $this->redirect('/profile/categoryconfigurator');      
        }
    }

    public function changeExpenseNameAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $editExpenseCategoryName = $_POST['editExpenseCategoryName'];
        if (!$editExpenseCategoryName) {
            ($this->redirect('/profile/categoryconfigurator'));
        }
        $editExpenseCategoryID = filter_input(INPUT_POST, 'expenseCategoryEditedID', FILTER_VALIDATE_INT);
        if (!$editExpenseCategoryID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->editExpensesCategory($editExpenseCategoryName, $editExpenseCategoryID)) {
            if(isset($_SESSION['expensesCatID'])) {
                unset($_SESSION['expensesCatID']);
            }
            Flash::addMessage('Zmiany zapisane');
            $this->redirect('/profile/categoryconfigurator');      
        }
    }

    public function setLimitOfExpenseAction()
    {
        // $setLimitValue = $_POST['limitValue'];
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $setLimitValue = filter_input(INPUT_POST, 'limitValue', FILTER_VALIDATE_INT);
        if (!$setLimitValue) {
            ($this->redirect('/profile/categoryconfigurator'));
        }

        $limitValueID = filter_input(INPUT_POST, 'limitID', FILTER_VALIDATE_INT);
        if (!$limitValueID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }

        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->setLimitValueDB($setLimitValue, $limitValueID)) {
            if(isset($_SESSION['idExpenseLimit'])) {
                unset($_SESSION['idExpenseLimit']);
            }
            Flash::addMessage('Zmiany zapisane');
            $this->redirect('/profile/categoryconfigurator');      
        }
    }

    public function changePayMethNameAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $editPayMethCategoryName = $_POST['editPayMethCategoryName'];
        if (!$editPayMethCategoryName) {
            ($this->redirect('/profile/categoryconfigurator'));
        }
        $editPaymentMethCategoryID = filter_input(INPUT_POST, 'payMethodEditedID', FILTER_VALIDATE_INT);
        if (!$editPaymentMethCategoryID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->editPayMethCategory($editPayMethCategoryName, $editPaymentMethCategoryID)) {
            if(isset($_SESSION['payMethCatID'])) {
                unset($_SESSION['payMethCatID']);
            }
            Flash::addMessage('Zmiany zapisane');
            $this->redirect('/profile/categoryconfigurator');      
        }
    }

    public function deleteIncomeCategoryDataBaseAction()
    {
        $deleteIncomeCategoryID = filter_input(INPUT_POST, 'incomeDeleteID', FILTER_VALIDATE_INT);
        if (!$deleteIncomeCategoryID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->deleteIncomesCategory($deleteIncomeCategoryID)&&($personalBudget->deleteIncomesRowRelatedToIncomesCatAssignedToUserId($deleteIncomeCategoryID))) {
            // if(isset($_SESSION['idIncomesDeleteCat'])) {
            //     unset($_SESSION['idIncomesDeleteCat']);
            // }
            Flash::addMessage('Pomyślnie usunięto kategorię oraz powiązane z nią przychody');
            $this->redirect('/profile/categoryconfigurator');      
        }       
    }

    public function deleteExpenseCategoryDataBaseAction()
    {
        $deleteExpenseCategoryID = filter_input(INPUT_POST, 'expenseDeleteID', FILTER_VALIDATE_INT);
        if (!$deleteExpenseCategoryID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->deleteExpensesCategory($deleteExpenseCategoryID)&&($personalBudget->deleteExpensesRowRelatedToExpensesCatAssignedToUserId($deleteExpenseCategoryID))) {
            // if(isset($_SESSION['idExpensesDeleteCat'])) {
            //     unset($_SESSION['idExpensesDeleteCat']);
            // }
            Flash::addMessage('Pomyślnie usunięto kategorię oraz powiązane z nią wydatki');
            $this->redirect('/profile/categoryconfigurator');      
        }       
    }

    public function deletePaymentMethodsCategoryDataBaseAction()
    {
        $deletePaymentMethodCategoryID = filter_input(INPUT_POST, 'payMethDeleteID', FILTER_VALIDATE_INT);
        if (!$deletePaymentMethodCategoryID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->deletePayMethCategory($deletePaymentMethodCategoryID)&&($personalBudget->deleteExpensesRowRelatedToPayMethCatAssignedToUserId($deletePaymentMethodCategoryID))) {
            // if(isset($_SESSION['idPayMethDeleteCat'])) {
            //     unset($_SESSION['idPayMethDeleteCat']);
            // }
            Flash::addMessage('Pomyślnie usunięto kategorię oraz powiązane z nią wydatki');
            $this->redirect('/profile/categoryconfigurator');      
        }       
    }

    public function deleteIncomesCategory()
    {
        // if(isset($_POST['deleteIncomesCatID'])) {
        //     $_SESSION['idIncomesDeleteCat'] = $_POST['deleteIncomesCatID'];
        // }

        $deleteIncomeCategoryID = filter_input(INPUT_POST, 'deleteIncomesCatID', FILTER_VALIDATE_INT);
        if (!$deleteIncomeCategoryID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }

        $name_income_category_to_delete = \App\Models\ModelPersonalBudget::selectNameFromIncomesCategoryToDelete($deleteIncomeCategoryID);

        View::renderTemplate('Profile/areYouSureDeleteIncomesCategory.html', [
            'user' => $this->user,
            'name_income_category_to_delete' => $name_income_category_to_delete
        ]);
    }

    public function deleteExpensesCategory()
    {
        // if(isset($_POST['deleteExpensesCatID'])) {
        //     $_SESSION['idExpensesDeleteCat'] = $_POST['deleteExpensesCatID'];
        // }

        $deleteExpenseCategoryID = filter_input(INPUT_POST, 'deleteExpensesCatID', FILTER_VALIDATE_INT);
        if (!$deleteExpenseCategoryID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }

        $name_expense_category_to_delete = \App\Models\ModelPersonalBudget::selectNameFromExpensesCategoryToDelete($deleteExpenseCategoryID);

        View::renderTemplate('Profile/areYouSureDeleteExpensesCategory.html', [
            'user' => $this->user,
            'name_expense_category_to_delete' => $name_expense_category_to_delete
        ]);
    }

    public function setLimitForExpense()
    {
        // if(isset($_POST['setExpenseLimit'])) {
        //     $_SESSION['idExpenseLimit'] = $_POST['setExpenseLimit'];
        // }
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }

        $idExpenseLimit = filter_input(INPUT_POST, 'setExpenseLimit', FILTER_VALIDATE_INT);
        if (!$idExpenseLimit) {
            ($this->redirect('/profile/categoryconfigurator'));
        }

        $set_limit_expense = \App\Models\ModelPersonalBudget::selectNameFromExpensesCategoryToLimit($idExpenseLimit);
        $limit_value = \App\Models\ModelPersonalBudget::selectValueOfLimit($idExpenseLimit);

        View::renderTemplate('Profile/setLimit.html', [
            'user' => $this->user,
            'set_limit_expense' => $set_limit_expense,
            'limit_value' => $limit_value,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function deletePaymentMethodsCategory()
    {
        // if(isset($_POST['deletePayMethCatID'])) {
        //     $_SESSION['idPayMethDeleteCat'] = $_POST['deletePayMethCatID'];
        // }

        $deletePayMethCategoryID = filter_input(INPUT_POST, 'deletePayMethCatID', FILTER_VALIDATE_INT);
        if (!$deletePayMethCategoryID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }

        $name_pay_meth_category_to_delete = \App\Models\ModelPersonalBudget::selectNameFromPayMethCategoryToDelete($deletePayMethCategoryID);

        View::renderTemplate('Profile/areYouSureDeletePayMethCategory.html', [
            'user' => $this->user,
            'name_pay_meth_category_to_delete' => $name_pay_meth_category_to_delete
        ]);
    }

    public function addNewIncomesCategory()
    {
        View::renderTemplate('Profile/addNewIncomesCategory.html', [
            'user' => $this->user,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function addToDataBaseNewIncomesCategory()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
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
            'user' => $this->user,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function addToDataBaseNewExpensesCategory()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
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
            'user' => $this->user,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function addToDataBaseNewPayMethCategory()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
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
