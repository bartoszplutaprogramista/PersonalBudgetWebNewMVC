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
        if (isset($_POST['editIncomesCat'])) {
            if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
                $this->redirect('/');
            }

            $editIncomesCategoryID = filter_input(INPUT_POST, 'editIncomesCat', FILTER_VALIDATE_INT);

            if (!$editIncomesCategoryID) {
                $this->redirect('/profile/categoryconfigurator');
            }
        } else {
            $editIncomesCategoryID = (int)$this->route_params['idincomeseditedcategory'];
            if (!$editIncomesCategoryID) {
                $this->redirect('/profile/categoryconfigurator');
            }
        }

        $name_income_category_to_edit = \App\Models\ModelPersonalBudget::selectNameFromIncomesCategoryToEdit($editIncomesCategoryID); 

        if (!$name_income_category_to_edit) {
            Flash::addMessage('Nie znaleziono kategorii lub nie należy do Twojego konta', Flash::WARNING);
            $this->redirect('/profile/categoryconfigurator');
        }

        View::renderTemplate('Profile/editIncomesCategory.html', [
            'user' => $this->user,
            'name_income_category_to_edit' => $name_income_category_to_edit,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function editExpensesCategory()
    {
        if (isset($_POST['editExpensesCat'])){
            if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
                $this->redirect('/');
            }
            $editExpensesCategoryID = filter_input(INPUT_POST, 'editExpensesCat', FILTER_VALIDATE_INT);
            if (!$editExpensesCategoryID) {
                $this->redirect('/profile/categoryconfigurator');
            }
        } else{

        $editExpensesCategoryID = (int)$this->route_params['idexpenseseditedcategory'];
            if (!$editExpensesCategoryID) {
                Flash::addMessage('Nie znaleziono kategorii lub nie należy do Twojego konta', Flash::WARNING);
                $this->redirect('/profile/categoryconfigurator');
            }
        }

        $name_expense_category_to_edit = \App\Models\ModelPersonalBudget::selectNameFromExpensesCategoryToEdit($editExpensesCategoryID);

        if (!$name_expense_category_to_edit) {
            $this->redirect('/profile/categoryconfigurator');
        }

        View::renderTemplate('Profile/editExpensesCategory.html', [
            'user' => $this->user,
            'name_expense_category_to_edit' => $name_expense_category_to_edit,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function editPaymentMethodCategory()
    {
        if (isset($_POST['editPaymentMethodCat'])){
            if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
                $this->redirect('/');
            }
            $editPaymentMethCategoryID = filter_input(INPUT_POST, 'editPaymentMethodCat', FILTER_VALIDATE_INT);
            if (!$editPaymentMethCategoryID) {
                $this->redirect('/profile/categoryconfigurator');
            } 
        }   else {
            $editPaymentMethCategoryID = (int)$this->route_params['idpaymentmethodeditedcategory'];
            if (!$editPaymentMethCategoryID) {
                Flash::addMessage('Nie znaleziono kategorii lub nie należy do Twojego konta', Flash::WARNING);
                $this->redirect('/profile/categoryconfigurator');
            }            
        }

        $name_pay_meth_category_to_edit = \App\Models\ModelPersonalBudget::selectNameFromPayMethCategoryToEdit($editPaymentMethCategoryID);

        if (!$name_pay_meth_category_to_edit) {
            $this->redirect('/profile/categoryconfigurator');
        }

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

        $categoryId = filter_input(INPUT_POST, 'incomeCategoryEditedID', FILTER_VALIDATE_INT);
        if (!$categoryId) {
            $this->redirect('/profile/categoryconfigurator');
        }

        $personalBudget = new ModelPersonalBudget($_POST);
        $result = $personalBudget->editIncomesCategory();

        if ($result === true) {
            Flash::addMessage('Zmiany zapisane');
            $this->redirect('/profile/categoryconfigurator'); 
        }

        foreach ($result as $error) {
            Flash::addMessage($error, Flash::WARNING);
        }

        $this->redirect('/profile/editincomescategory/' . $categoryId);   
        
    }

    public function changeExpenseNameAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $categoryId = filter_input(INPUT_POST, 'expenseCategoryEditedID', FILTER_VALIDATE_INT);
        if (!$categoryId) {
            $this->redirect('/profile/categoryconfigurator');
        }
        $personalBudget = new ModelPersonalBudget($_POST);
        $result = $personalBudget->editExpensesCategory();

        if ($result === true) {
            Flash::addMessage('Zmiany zapisane');
            $this->redirect('/profile/categoryconfigurator'); 
        }

        foreach ($result as $error) {
            Flash::addMessage($error, Flash::WARNING);
        }

        $this->redirect('/profile/editexpensescategory/' . $categoryId); 
    }

    public function setLimitOfExpenseAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $idLimitValue = filter_input(INPUT_POST, 'limitID', FILTER_VALIDATE_INT);
        if (!$idLimitValue) {
            ($this->redirect('/profile/categoryconfigurator'));
        }
        $personalBudget = new ModelPersonalBudget($_POST);

        $result = $personalBudget->setLimitValueDB();

        if ($result === true) {
            Flash::addMessage('Zmiany zapisane');
            $this->redirect('/profile/categoryconfigurator'); 
        }

        foreach ($result as $error) {
            Flash::addMessage($error, Flash::WARNING);
        }

        $this->redirect('/profile/setlimitforexpense/' . $idLimitValue); 
    }

    public function changePayMethNameAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }

        $categoryId = filter_input(INPUT_POST, 'payMethodEditedID', FILTER_VALIDATE_INT);
        if (!$categoryId) {
            ($this->redirect('/profile/categoryconfigurator'));
        }
        $personalBudget = new ModelPersonalBudget($_POST);
        $result = $personalBudget->editPayMethCategory();

        if ($result === true) {
            Flash::addMessage('Zmiany zapisane');
            $this->redirect('/profile/categoryconfigurator'); 
        }

        foreach ($result as $error) {
            Flash::addMessage($error, Flash::WARNING);
        }

        $this->redirect('/profile/editpaymentmethodcategory/' . $categoryId); 
    }

    public function deleteIncomeCategoryDataBaseAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $deleteIncomeCategoryID = filter_input(INPUT_POST, 'incomeDeleteID', FILTER_VALIDATE_INT);
        if (!$deleteIncomeCategoryID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->deleteIncomesCategory($deleteIncomeCategoryID)&&($personalBudget->deleteIncomesRowRelatedToIncomesCatAssignedToUserId($deleteIncomeCategoryID))) {
            Flash::addMessage('Pomyślnie usunięto kategorię oraz powiązane z nią przychody');
            $this->redirect('/profile/categoryconfigurator');      
        }       
    }

    public function deleteExpenseCategoryDataBaseAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $deleteExpenseCategoryID = filter_input(INPUT_POST, 'expenseDeleteID', FILTER_VALIDATE_INT);
        if (!$deleteExpenseCategoryID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->deleteExpensesCategory($deleteExpenseCategoryID)&&($personalBudget->deleteExpensesRowRelatedToExpensesCatAssignedToUserId($deleteExpenseCategoryID))) {
            Flash::addMessage('Pomyślnie usunięto kategorię oraz powiązane z nią wydatki');
            $this->redirect('/profile/categoryconfigurator');      
        }       
    }

    public function deletePaymentMethodsCategoryDataBaseAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $deletePaymentMethodCategoryID = filter_input(INPUT_POST, 'payMethDeleteID', FILTER_VALIDATE_INT);
        if (!$deletePaymentMethodCategoryID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->deletePayMethCategory($deletePaymentMethodCategoryID)&&($personalBudget->deleteExpensesRowRelatedToPayMethCatAssignedToUserId($deletePaymentMethodCategoryID))) {
            Flash::addMessage('Pomyślnie usunięto kategorię oraz powiązane z nią wydatki');
            $this->redirect('/profile/categoryconfigurator');      
        }       
    }

    public function deleteIncomesCategory()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }

        $deleteIncomeCategoryID = filter_input(INPUT_POST, 'deleteIncomesCatID', FILTER_VALIDATE_INT);
        if (!$deleteIncomeCategoryID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }

        $name_income_category_to_delete = \App\Models\ModelPersonalBudget::selectNameFromIncomesCategoryToDelete($deleteIncomeCategoryID);

        View::renderTemplate('Profile/areYouSureDeleteIncomesCategory.html', [
            'user' => $this->user,
            'name_income_category_to_delete' => $name_income_category_to_delete,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function deleteExpensesCategory()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }

        $deleteExpenseCategoryID = filter_input(INPUT_POST, 'deleteExpensesCatID', FILTER_VALIDATE_INT);
        if (!$deleteExpenseCategoryID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }

        $name_expense_category_to_delete = \App\Models\ModelPersonalBudget::selectNameFromExpensesCategoryToDelete($deleteExpenseCategoryID);

        View::renderTemplate('Profile/areYouSureDeleteExpensesCategory.html', [
            'user' => $this->user,
            'name_expense_category_to_delete' => $name_expense_category_to_delete,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function setLimitForExpense()
    {
        if (isset($_POST['setExpenseLimit'])){
            if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
                $this->redirect('/');
            }
            $idExpenseLimit = filter_input(INPUT_POST, 'setExpenseLimit', FILTER_VALIDATE_INT);
            if (!$idExpenseLimit) {
                ($this->redirect('/profile/categoryconfigurator'));
            }
        } else {
            $idExpenseLimit = (int)$this->route_params['idlimit'];
        }

        $set_limit_expense = \App\Models\ModelPersonalBudget::selectNameFromExpensesCategoryToLimit($idExpenseLimit);
        if ($set_limit_expense === false) {
            Flash::addMessage('Nie znaleziono kategorii lub nie należy do Twojego konta', Flash::WARNING);
            $this->redirect('/profile/categoryconfigurator');
        }
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
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }

        $deletePayMethCategoryID = filter_input(INPUT_POST, 'deletePayMethCatID', FILTER_VALIDATE_INT);
        if (!$deletePayMethCategoryID) {
            ($this->redirect('/profile/categoryconfigurator'));
        }

        $name_pay_meth_category_to_delete = \App\Models\ModelPersonalBudget::selectNameFromPayMethCategoryToDelete($deletePayMethCategoryID);

        View::renderTemplate('Profile/areYouSureDeletePayMethCategory.html', [
            'user' => $this->user,
            'name_pay_meth_category_to_delete' => $name_pay_meth_category_to_delete,
            'csrf_token' => Csrf::generate()
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
        $personalBudget = new ModelPersonalBudget($_POST);
        $result = $personalBudget->addNewIncomesCategory();

        if ($result === true) {
            Flash::addMessage('Zmiany zapisane');
            $this->redirect('/profile/categoryconfigurator'); 
        }

        foreach ($result as $error) {
            Flash::addMessage($error, Flash::WARNING);
        }

        $this->redirect('/profile/addnewincomescategory');
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
        $personalBudget = new ModelPersonalBudget($_POST);

        $result = $personalBudget->addNewExpensesCategory();

        if ($result === true) {
            Flash::addMessage('Zmiany zapisane');
            $this->redirect('/profile/categoryconfigurator'); 
        }

        foreach ($result as $error) {
            Flash::addMessage($error, Flash::WARNING);
        }

        $this->redirect('/profile/addnewexpensescategory');
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
        $personalBudget = new ModelPersonalBudget($_POST);
        $result = $personalBudget->addNewPayMethCategory();

        if ($result === true) {
            Flash::addMessage('Zmiany zapisane');
            $this->redirect('/profile/categoryconfigurator'); 
        }

        foreach ($result as $error) {
            Flash::addMessage($error, Flash::WARNING);
        }

        $this->redirect('/profile/addnewpaymethcategory');
    }
    
    public function deleteDataBaseAccount()
    {
        $personalBudget = new ModelPersonalBudget($_POST);
        $userID = $_SESSION['userIdSession'];
        Auth::logout();

        if ($personalBudget->deleteAccountFromDataBase($userID)) {
            $this->redirect('/login/show-message-after-deleting-user-data');
        }
    }

}
