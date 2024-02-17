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
        $editIncomesCateegoryID = $_POST['editIncomesCat'];
        $_SESSION['incomesCatID'] = $editIncomesCateegoryID;

        View::renderTemplate('Profile/editIncomesCategory.html', [
            'user' => $this->user
        ]);
    }

    public function changeIncomeNameAction()
    {
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->editIncomesCategory()) {
            Flash::addMessage('Zmiany zapisane');
            $this->redirect('/profile/categoryconfigurator');      
        }
    }

    public function deleteIncomesCategory()
    {
        if(isset($_POST['deleteIncomesCatID'])) {
            $_SESSION['idIncomesDeleteCat'] = $_POST['deleteIncomesCatID'];
        }

        // if(isset($_POST['myOrdinalNumberDeleteCategoryIncomes'])) {

        //     $_SESSION['myOrdinalNumberDeleteIncomesVar'] = $_POST['myOrdinalNumberDeleteCategoryIncomes'];
        // }
        View::renderTemplate('Profile/areYouSureDeleteIncomesCategory.html', [
            'user' => $this->user
        ]);
        // $this->redirect('/personalbudget/successareyousuredeletefromincomes');
    }

    // public function sucessAreYouSureDeleteCategoryFromIncomes()
    // {
        
    // }
}
