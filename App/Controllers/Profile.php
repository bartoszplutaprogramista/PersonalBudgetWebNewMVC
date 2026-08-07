<?php

namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\ModelPersonalBudget;
use \App\Csrf;
// use App\Models\User;


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
        // $editIncomesCategoryID = $_POST['editIncomesCat'];

        if (isset($_POST['editIncomesCat'])) {
            if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
                $this->redirect('/');
            }

            $editIncomesCategoryID = filter_input(INPUT_POST, 'editIncomesCat', FILTER_VALIDATE_INT);

            if (!$editIncomesCategoryID) {
                $this->redirect('/profile/categoryconfigurator');
            }
            // $_SESSION['editIncomesCategoryID'] = $editIncomesCategoryID;
        // $_SESSION['incomesCatID'] = $editIncomesCategoryID;
        } else {

        // $editIncomesCategoryID = $_SESSION['editIncomesCategoryID'] ?? null;
            $editIncomesCategoryID = (int)$this->route_params['idincomeseditedcategory'];
            if (!$editIncomesCategoryID) {
                $this->redirect('/profile/categoryconfigurator');
            }
        }

        $name_income_category_to_edit = \App\Models\ModelPersonalBudget::selectNameFromIncomesCategoryToEdit($editIncomesCategoryID); 

        if (!$name_income_category_to_edit) {
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
        
        // $editExpensesCategoryID = $_POST['editExpensesCat'];
        // $editExpensesCategoryID = filter_input(INPUT_POST, 'editExpensesCat', FILTER_VALIDATE_INT);
        // $_SESSION['editExpensesCategoryID'] = $editExpensesCategoryID;
        //  if (!$editExpensesCategoryID) {
        //         $this->redirect('/profile/categoryconfigurator');
            // }
            $editExpensesCategoryID = filter_input(INPUT_POST, 'editExpensesCat', FILTER_VALIDATE_INT);
            if (!$editExpensesCategoryID) {
                $this->redirect('/profile/categoryconfigurator');
            }
        } else{

        $editExpensesCategoryID = (int)$this->route_params['idexpenseseditedcategory'];
            if (!$editExpensesCategoryID) {
                $this->redirect('/profile/categoryconfigurator');
            }
        }
        // if (!$editExpensesCategoryID) {
        //         $this->redirect('/profile/categoryconfigurator');
        // }

        // $editExpensesCategoryID = $_SESSION['editExpensesCategoryID'] ?? null;
        // $editExpensesCategoryID = filter_input(INPUT_POST, 'editExpensesCat', FILTER_VALIDATE_INT);
        // if (!$editExpensesCategoryID) {
        //     $this->redirect('/profile/categoryconfigurator');
        // }

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
                $this->redirect('/profile/categoryconfigurator');
            }            
        }
        // $editPaymentMethCategoryID = $_POST['editPaymentMethodCat'];

        // $_SESSION['payMethCatID'] = $editPaymentMethCategoryID;

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
        /*************** */

    //     $editIncomeCategoryName = mb_substr(trim($_POST['editIncomeCategoryName'] ?? ''), 0, 50);
    //     if ($editIncomeCategoryName === '') {
    //         Flash::addMessage('Nazwa kategorii jest wymagana', Flash::WARNING);
    //         $this->redirect('/profile/categoryconfigurator');
    //     }
    //     // $editIncomeCategoryName = $_POST['editIncomeCategoryName'];
    //     if (!$editIncomeCategoryName) {
    //         ($this->redirect('/profile/categoryconfigurator'));
    //     }
    //     $editIncomeCategoryID = filter_input(INPUT_POST, 'incomeCategoryEditedID', FILTER_VALIDATE_INT);
    //     if (!$editIncomeCategoryID) {
    //         ($this->redirect('/profile/categoryconfigurator'));
    //     }
    //     // $editIncomeCategoryID = $_POST['incomeCategoryEditedID'];
    //     $personalBudget = new ModelPersonalBudget($_POST);
    //     if ($personalBudget->editIncomesCategory($editIncomeCategoryName, $editIncomeCategoryID)) {
    //         // if(isset($_SESSION['incomesCatID'])) {
    //         //     unset($_SESSION['incomesCatID']);
    //         // }
    //         Flash::addMessage('Zmiany zapisane');
    //         $this->redirect('/profile/categoryconfigurator');      
    //     }
    // }

    public function changeExpenseNameAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        
        // $editExpenseCategoryName = mb_substr(trim($_POST['editExpenseCategoryName'] ?? ''), 0, 50);
        // if ($editExpenseCategoryName === '') {
        //     Flash::addMessage('Nazwa kategorii jest wymagana', Flash::WARNING);
        //     $this->redirect('/profile/categoryconfigurator');
        // }
        // // $editExpenseCategoryName = $_POST['editExpenseCategoryName'];
        // if (!$editExpenseCategoryName) {
        //     ($this->redirect('/profile/categoryconfigurator'));
        // }
        // $editExpenseCategoryID = filter_input(INPUT_POST, 'expenseCategoryEditedID', FILTER_VALIDATE_INT);
        // if (!$editExpenseCategoryID) {
        //     ($this->redirect('/profile/categoryconfigurator'));
        // }
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


        // if ($personalBudget->editExpensesCategory($editExpenseCategoryName, $editExpenseCategoryID)) {
        //     if(isset($_SESSION['expensesCatID'])) {
        //         unset($_SESSION['expensesCatID']);
        //     }
        //     Flash::addMessage('Zmiany zapisane');
        //     $this->redirect('/profile/categoryconfigurator');      
        // }
    }

    public function setLimitOfExpenseAction()
    {
        // $setLimitValue = $_POST['limitValue'];
        // if (isset($_POST['limitID'])){
            if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
                $this->redirect('/');
            }
        // }
        $idLimitValue = filter_input(INPUT_POST, 'limitID', FILTER_VALIDATE_INT);
        if (!$idLimitValue) {
            ($this->redirect('/profile/categoryconfigurator'));
        }

        // $limitValueID = filter_input(INPUT_POST, 'limitID', FILTER_VALIDATE_INT);
        // if (!$limitValueID) {
        //     ($this->redirect('/profile/categoryconfigurator'));
        // }

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


        // if ($personalBudget->setLimitValueDB($setLimitValue, $limitValueID)) {
        // if ($personalBudget->setLimitValueDB()) {
        //     // if(isset($_SESSION['idExpenseLimit'])) {
        //     //     unset($_SESSION['idExpenseLimit']);
        //     // }
        //     Flash::addMessage('Zmiany zapisane');
        //     $this->redirect('/profile/categoryconfigurator');      
        // }
    }

    public function changePayMethNameAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        // $editPayMethCategoryName = mb_substr(trim($_POST['editPayMethCategoryName'] ?? ''), 0, 50);
        // if ($editPayMethCategoryName === '') {
        //     Flash::addMessage('Nazwa kategorii jest wymagana', Flash::WARNING);
        //     $this->redirect('/profile/categoryconfigurator');
        // }
        // // $editPayMethCategoryName = $_POST['editPayMethCategoryName'];
        // if (!$editPayMethCategoryName) {
        //     ($this->redirect('/profile/categoryconfigurator'));
        // }
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

        /////////złe
        // $personalBudget = new ModelPersonalBudget($_POST);
        // if ($personalBudget->editPayMethCategory()) {
        //     // if(isset($_SESSION['payMethCatID'])) {
        //     //     unset($_SESSION['payMethCatID']);
        //     // }
        //     Flash::addMessage('Zmiany zapisane');
        //     $this->redirect('/profile/categoryconfigurator');      
        // }
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
            // if(isset($_SESSION['idIncomesDeleteCat'])) {
            //     unset($_SESSION['idIncomesDeleteCat']);
            // }
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
            // if(isset($_SESSION['idExpensesDeleteCat'])) {
            //     unset($_SESSION['idExpensesDeleteCat']);
            // }
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
        // if(isset($_POST['deleteExpensesCatID'])) {
        //     $_SESSION['idExpensesDeleteCat'] = $_POST['deleteExpensesCatID'];
        // }
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
        // if(isset($_POST['setExpenseLimit'])) {
        //     $_SESSION['idExpenseLimit'] = $_POST['setExpenseLimit'];
        // }
        if (isset($_POST['setExpenseLimit'])){
            if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
                $this->redirect('/');
            }
            $idExpenseLimit = filter_input(INPUT_POST, 'setExpenseLimit', FILTER_VALIDATE_INT);
        // echo $idExpenseLimit;
        // exit;
            if (!$idExpenseLimit) {
                ($this->redirect('/profile/categoryconfigurator'));
            }
        } else {
            $idExpenseLimit = (int)$this->route_params['idlimit'];
        }

        

        // $idExpenseLimit = filter_input(INPUT_POST, 'setExpenseLimit', FILTER_VALIDATE_INT);
        // echo $idExpenseLimit;
        // exit;
        // if (!$idExpenseLimit) {
        //     ($this->redirect('/profile/categoryconfigurator'));
        // }

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
        // if(isset($_POST['deletePayMethCatID'])) {
        //     $_SESSION['idPayMethDeleteCat'] = $_POST['deletePayMethCatID'];
        // }
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
        // $formData = $_SESSION['addIncomeName'] ?? null;
        // unset($_SESSION['addIncomeName']);

        // echo '<pre>';
        // print_r($formData);
        // echo '</pre>';


        View::renderTemplate('Profile/addNewIncomesCategory.html', [
            'user' => $this->user,
            // 'formData' => $formData,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function addToDataBaseNewIncomesCategory()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        // $newIncomeCat = mb_substr(trim($_POST['addedNewIncomeCat'] ?? ''), 0, 50);
        // if ($newIncomeCat === '') {
        //     Flash::addMessage('Nazwa kategorii jest wymagana', Flash::WARNING);
        //     $this->redirect('/profile/categoryconfigurator');
        // }
        //złę $newIncomeCat = $_POST['addedNewIncomeCat'];

        // $_SESSION['addIncomeName'] = $_POST;

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
        // if ($personalBudget->addNewIncomesCategory($newIncomeCat)) {
        //     Flash::addMessage('Dodano nową kategorię');
        //     $this->redirect('/profile/categoryconfigurator');      
        // }
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
        // $newExpenseCat = mb_substr(trim($_POST['addedNewExpenseCat'] ?? ''), 0, 50);
        // if ($newExpenseCat === '') {
        //     Flash::addMessage('Nazwa kategorii jest wymagana', Flash::WARNING);
        //     $this->redirect('/profile/categoryconfigurator');
        // }
        // $newExpenseCat = $_POST['addedNewExpenseCat'];
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

        // if ($personalBudget->addNewExpensesCategory($newExpenseCat)) {
        //     Flash::addMessage('Dodano nową kategorię');
        //     $this->redirect('/profile/categoryconfigurator');      
        // }
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
        // $newPayMethCat = mb_substr(trim($_POST['addedNewPayMethCat'] ?? ''), 0, 50);
        // if ($newPayMethCat === '') {
        //     Flash::addMessage('Nazwa kategorii jest wymagana', Flash::WARNING);
        //     $this->redirect('/profile/categoryconfigurator');
        // }
        // $newPayMethCat = $_POST['addedNewPayMethCat'];

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


        // $personalBudget = new ModelPersonalBudget($_POST);
        // if ($personalBudget->addNewPayMethCategory($newPayMethCat)) {
        //     Flash::addMessage('Dodano nową kategorię');
        //     $this->redirect('/profile/categoryconfigurator');      
        // }
    }

    // public function deleteDataBaseAccount()
    // {
    //     $userID = $_SESSION['userIdSession'];
    //     Auth::logout();
    //     $personalBudget = new ModelPersonalBudget($_POST);
    //     if (($personalBudget->deleteFromDataBaseIncomesUserID($userID))&&($personalBudget->deleteFromDataBaseExpensesUserID($userID))&&($personalBudget->deleteFromDataBaseIncomesCategoryAssignedToUser($userID))&&($personalBudget->deleteFromDataBaseExpensesCategoryAssignedToUser($userID))&&($personalBudget->deleteFromDataBasePaymentMethodsCategoryAssignedToUser($userID))&&($personalBudget->deleteFromDataBaseUser($userID))) {          
    //         $this->redirect('/login/show-message-after-deleting-user-data');
    //     }
    // }


    // Kontroler Profile.php - uproszczony
    public function deleteDataBaseAccount()
    {
        // $user = $this->user;

        // var_dump($user);
        // exit;
        $personalBudget = new ModelPersonalBudget($_POST);
        $userID = $_SESSION['userIdSession'];
        Auth::logout();

        // if ((new User)->deleteAccountFromDataBase($userID)) {
        if ($personalBudget->deleteAccountFromDataBase($userID)) {
            $this->redirect('/login/show-message-after-deleting-user-data');
        }
    }

}
