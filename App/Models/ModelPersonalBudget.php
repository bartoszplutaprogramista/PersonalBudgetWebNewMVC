<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;

#[\AllowDynamicProperties]
class ModelPersonalBudget extends \Core\Model
{

    public $amountIncome;
    public $dateIncome;
    public $commentIncome;
    // public $userId;
    public $email;
    public $paymentCategoryIncomeName;
 
    public function updateIncomes()
    {
        $personalBudget = new ModelPersonalBudget($_POST);
        $db = static::getDB();
        $catIncomeId = $personalBudget->getpaymentCategoryIncomeId();
        $amountIncome = $_POST['amountIncome'];
        $dateIncome = $_POST['dateIncome'];
        $commentIncome = $_POST['commentIncome'];

        $queryEditIncome = $db->prepare('UPDATE incomes 
            SET income_category_assigned_to_user_id  = :income_category,  
            amount = :amount,
            date_of_income = :dateIncome,
            income_comment  = :commentIncome
            WHERE id=:incomeEditId');
		$queryEditIncome->bindValue(':income_category', $catIncomeId, PDO::PARAM_INT);
		$queryEditIncome->bindValue(':amount', $amountIncome, PDO::PARAM_STR);
		$queryEditIncome->bindValue(':dateIncome', $dateIncome, PDO::PARAM_STR);
		$queryEditIncome->bindValue(':commentIncome', $commentIncome, PDO::PARAM_STR);
        $queryEditIncome->bindValue(':incomeEditId', $_SESSION['idIncomesEditRow'], PDO::PARAM_INT);
        
        return $queryEditIncome->execute();
    }

    public function updateExpenses()
    {
        $personalBudget = new ModelPersonalBudget($_POST);
        $db = static::getDB();
        $paymentCatExpenseId = $personalBudget->getpaymentCategoryExpenseId();
        $paymentId = $personalBudget->getPaymentId();
        $amountExpense = $_POST['amountExpense'];
        $dateExpense = $_POST['dateExpense'];
        $commentExpense = $_POST['commentExpense'];

        $queryEditExpense = $db->prepare('UPDATE expenses 
            SET expense_category_assigned_to_user_id = :expense_category,  
            payment_method_assigned_to_user_id = :payment_method,
            amount = :amount,
            date_of_expense = :dateExpense,
            expense_comment = :commentExpense
            WHERE id=:expenseEditId');
		$queryEditExpense->bindValue(':expense_category', $paymentCatExpenseId, PDO::PARAM_INT);
		$queryEditExpense->bindValue(':payment_method', $paymentId, PDO::PARAM_INT);
		$queryEditExpense->bindValue(':amount', $amountExpense, PDO::PARAM_STR);
		$queryEditExpense->bindValue(':dateExpense', $dateExpense, PDO::PARAM_STR);
		$queryEditExpense->bindValue(':commentExpense', $commentExpense, PDO::PARAM_STR);
        $queryEditExpense->bindValue(':expenseEditId', $_SESSION['idExpensesEditRow'], PDO::PARAM_INT);
        
        return $queryEditExpense->execute();


        

        // $amountExpense = $_POST['amountExpense'];
        // $dateExpense = $_POST['dateExpense'];
        // $commentExpense = $_POST['commentExpense'];

        // $queryExpense = $db->prepare('INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) VALUES (:userId, :expense_category, :payment_method, :amount, :dateExpense, :commentExpense)');	
		// $queryExpense->bindValue(':userId', $userId, PDO::PARAM_INT);
		// $queryExpense->bindValue(':expense_category', $paymentCatExpenseId, PDO::PARAM_INT);
		// $queryExpense->bindValue(':payment_method', $paymentId, PDO::PARAM_INT);
		// $queryExpense->bindValue(':amount', $amountExpense, PDO::PARAM_STR);
		// $queryExpense->bindValue(':dateExpense', $dateExpense, PDO::PARAM_STR);
		// $queryExpense->bindValue(':commentExpense', $commentExpense, PDO::PARAM_STR);
    }

    public static function selectAllFromIncomesToEdit($idIncomesEdit)
    {
        $db = static::getDB();
        $queryEditIncome = $db->prepare('SELECT 
        inc.amount AS amn,
        inc.date_of_income AS dateInc,
        incCat.name AS incCategory,
        inc.income_comment AS comment,
        inc.id AS incID
        FROM incomes_category_assigned_to_users AS incCat
        INNER JOIN incomes AS inc ON incCat.id = inc.income_category_assigned_to_user_id 
        WHERE inc.id = :id');
        $queryEditIncome->bindValue(':id', $idIncomesEdit, PDO::PARAM_INT);
        $queryEditIncome->execute();

        $queryName = $queryEditIncome->fetchAll();   
        return $queryName;        
    }

    public static function selectAllFromExpensesToEdit($idExpensesEdit)
    {
        $db = static::getDB();
        $queryEditExpenses = $db->prepare('SELECT 
        ex.amount AS amn,
        ex.date_of_expense AS dateExp,
        pay.name AS pay,
        exCat.name AS excategory,
        ex.expense_comment AS comment,
        ex.id AS exID      
        FROM expenses_category_assigned_to_users AS exCat 
        INNER JOIN expenses AS ex ON exCat.id = ex.expense_category_assigned_to_user_id 
        INNER JOIN payment_methods_assigned_to_users AS pay ON ex.payment_method_assigned_to_user_id = pay.id
        WHERE ex.id = :id');
        $queryEditExpenses->bindValue(':id', $idExpensesEdit, PDO::PARAM_INT);
        $queryEditExpenses->execute();

        $queryName = $queryEditExpenses->fetchAll();   
        return $queryName;
    }

    public function deleteIncome()
    {
        $db = static::getDB();
        $queryDeleteIncome = $db->prepare('DELETE FROM incomes WHERE id = :idOfRow');
        $queryDeleteIncome->bindValue(':idOfRow', $_SESSION['idIncomesDelete'], PDO::PARAM_INT);
        $queryDeleteIncome->execute();

        // echo "Id wynosi: ".$id;

        return $queryDeleteIncome;
        // return $id;
    }

    public function deleteExpense()
    {
        $db = static::getDB();
        $queryDeleteExpense = $db->prepare('DELETE FROM expenses WHERE id = :idOfRow');
        $queryDeleteExpense->bindValue(':idOfRow', $_SESSION['idExpensesDelete'], PDO::PARAM_INT);
        $queryDeleteExpense->execute();

        // echo "Id wynosi: ".$id;

        return $queryDeleteExpense;
        // return $id;
    }

    // public function getRegisteredUser()
    // {

    //     $queryId = $db->prepare('SELECT id FROM users WHERE name = :userName');	
    //     $queryId->bindValue(':userName', $nick, PDO::PARAM_STR);
    //     $queryId->execute();
    
    //     $userId = $queryId->fetch();
    // }

    public static function getQueryIncomesNameDefault()
    {
        $db = static::getDB();
        $queryNameDefault = $db->prepare('SELECT name FROM incomes_category_default');	
        $queryNameDefault->execute();

        $queryName = $queryNameDefault->fetchAll();

        return $queryName;
    }

    public static function getQueryExpensesNameDefault()
    {
        $db = static::getDB();
        $queryNameExpenseCategoryDefault = $db->prepare('SELECT name FROM expenses_category_default');	
        $queryNameExpenseCategoryDefault->execute();

        $queryNameExpenses = $queryNameExpenseCategoryDefault->fetchAll();

        return $queryNameExpenses;
    }

    public static function getDateCurrentMonth()
    {
        $dataHelpYearMonth = date("Y-m");
        $dataHelpCurrentMonth = $dataHelpYearMonth."%";

        return $dataHelpCurrentMonth;
    }  

    public static function getDateLastMonth()
    {
        $timeMonth = date('m', strtotime("-1 MONTH"));
		$timeYear = date('Y', strtotime("-1 MONTH"));

        $fullDateLastMonth = $timeYear."-".$timeMonth."%";

        return $fullDateLastMonth;
    } 

    public static function getDateCurrentYear()
    {
        $dateCurrentYear = date("Y");    
        $fullDateCurrentYear = $dateCurrentYear."%";
        
        return $fullDateCurrentYear;
    }

    // public static function getQueryNameIncome($dataHelp)
    // {
    //     $db = static::getDB();
    //     $queryNameIncome = $db->prepare('SELECT * FROM incomes_category_assigned_to_users INNER JOIN incomes ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id WHERE incomes.user_id = :userId AND date_of_income LIKE :dataHelpCurrentMonth ORDER BY date_of_income DESC');
    //     $queryNameIncome->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
    //     $queryNameIncome->bindValue(':dataHelpCurrentMonth', $dataHelp, PDO::PARAM_STR);
    //     $queryNameIncome->execute();

    //     $queryName = $queryNameIncome->fetchAll();   
    //     return $queryName;
    // }

    public static function getQueryNameIncome($dataHelp)
    {
        $db = static::getDB();
        $queryNameIncome = $db->prepare('SELECT 
        inc.amount AS amn,
        inc.date_of_income AS dateInc,
        incCat.name AS incCategory,
        inc.income_comment AS comment,
        inc.id AS incID
        FROM incomes_category_assigned_to_users AS incCat
        INNER JOIN incomes AS inc ON incCat.id = inc.income_category_assigned_to_user_id 
        WHERE inc.user_id = :userId AND date_of_income LIKE :dataHelp 
        ORDER BY date_of_income DESC');
        $queryNameIncome->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryNameIncome->bindValue(':dataHelp', $dataHelp, PDO::PARAM_STR);
        $queryNameIncome->execute();

        $queryName = $queryNameIncome->fetchAll();   
        return $queryName;
    }

    public static function getQueryNameExpense($dataHelp)
    {
        $db = static::getDB();
        $queryNameExpense = $db->prepare('SELECT 
        ex.amount AS amn,
        ex.date_of_expense AS dateExp,
        pay.name AS pay,
        exCat.name AS excategory,
        ex.expense_comment AS comment,
        ex.id AS exID
        FROM expenses_category_assigned_to_users AS exCat 
        INNER JOIN expenses AS ex ON exCat.id = ex.expense_category_assigned_to_user_id 
        INNER JOIN payment_methods_assigned_to_users AS pay ON ex.payment_method_assigned_to_user_id = pay.id
        WHERE ex.user_id = :userId AND date_of_expense LIKE :dataHelp 
        ORDER BY date_of_expense DESC');
        $queryNameExpense->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryNameExpense->bindValue(':dataHelp', $dataHelp, PDO::PARAM_STR);
        $queryNameExpense->execute();

        $queryExpense = $queryNameExpense->fetchAll();
        
        return $queryExpense;
    }

    public static function sumOfNamesFromIncomesToChart($dataHelp)
    {
        $db = static::getDB();
        $querySumIncomes = $db->prepare('SELECT 
        income_category_assigned_to_user_id AS inc_assigned_id, 
        SUM(amount) AS incNameSum, 
        inc_cat.name AS catName
        FROM incomes AS inc
        INNER JOIN incomes_category_assigned_to_users AS inc_cat ON inc_cat.id = inc.income_category_assigned_to_user_id
        WHERE inc.user_id = :userId AND date_of_income LIKE :dataHelp
        GROUP BY income_category_assigned_to_user_id
        ORDER BY incNameSum DESC');
        $querySumIncomes->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $querySumIncomes->bindValue(':dataHelp', $dataHelp, PDO::PARAM_STR);
        $querySumIncomes->execute();

        $incomesSumToChart = $querySumIncomes->fetchAll();
        
        return $incomesSumToChart;
    }

    public static function sumOfNamesFromExpensesToChart($dataHelp)
    {
        $db = static::getDB();
        $querySumExpenses = $db->prepare('SELECT 
        expense_category_assigned_to_user_id AS exp_assigned_id, 
        SUM(amount) AS expNameSum, 
        exp_cat.name AS catName
        FROM expenses AS exp
        INNER JOIN expenses_category_assigned_to_users AS exp_cat ON exp_cat.id = exp.expense_category_assigned_to_user_id
        WHERE exp.user_id = :userId AND date_of_expense LIKE :dataHelp
        GROUP BY expense_category_assigned_to_user_id
        ORDER BY expNameSum DESC');
        $querySumExpenses->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $querySumExpenses->bindValue(':dataHelp', $dataHelp, PDO::PARAM_STR);
        $querySumExpenses->execute();

        $expensesSumToChart = $querySumExpenses->fetchAll();
        
        return $expensesSumToChart;
    }

    public static function incomesSum($dataHelp)
    {
        $db = static::getDB();
        $querySumIncomes = $db->prepare('SELECT SUM(amount) AS incSum FROM incomes WHERE user_id = :userId AND date_of_income LIKE :dataHelpCurrentMonth');
        $querySumIncomes->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $querySumIncomes->bindValue(':dataHelpCurrentMonth', $dataHelp, PDO::PARAM_STR);
        $querySumIncomes->execute();

        $incomesSum = $querySumIncomes->fetch();
        
        return $incomesSum;
    }

    public static function expensesSum($dataHelp)
    {
        $db = static::getDB();
        $querySumExpenses = $db->prepare('SELECT SUM(amount) AS expSum FROM expenses WHERE user_id = :userId AND date_of_expense LIKE :dataHelpCurrentMonth');
        $querySumExpenses->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $querySumExpenses->bindValue(':dataHelpCurrentMonth', $dataHelp, PDO::PARAM_STR);
        $querySumExpenses->execute();

        $expensesSum = $querySumExpenses->fetch();

        return $expensesSum;
    }

    public static function getStartDateSelectedPeriod()
    {
        if (isset($_SESSION['start_date']))
        return $_SESSION['start_date'];
    }
    
    public static function getEndDateSelectedPeriod()
    {
        if (isset($_SESSION['end_date']))
        return $_SESSION['end_date'];
    }

    public static function getSelectedPeriodQueryNameIncome()
    {
        $startDate = ModelPersonalBudget::getStartDateSelectedPeriod();
        $endDate = ModelPersonalBudget::getEndDateSelectedPeriod();
        $db = static::getDB();
        $queryNameSelectedPeriod = $db->prepare('SELECT 
        inc.amount AS amn,
        inc.date_of_income AS dateInc,
        incCat.name AS incCategory,
        inc.income_comment AS comment,
        inc.id AS incID
        FROM incomes_category_assigned_to_users AS incCat
        INNER JOIN incomes AS inc ON incCat.id = inc.income_category_assigned_to_user_id WHERE inc.user_id = :userId AND date_of_income >= :dateSelectedPeriod1 AND date_of_income <= :dateSelectedPeriod2 ORDER BY date_of_income DESC');
        $queryNameSelectedPeriod->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryNameSelectedPeriod->bindValue(':dateSelectedPeriod1', $startDate, PDO::PARAM_STR);
        $queryNameSelectedPeriod->bindValue(':dateSelectedPeriod2', $endDate, PDO::PARAM_STR);
        $queryNameSelectedPeriod->execute();

        $queryName = $queryNameSelectedPeriod->fetchAll();

        return $queryName;
    }
    public static function getSelectedPeriodQueryNameExpense()
    {
        $startDate = ModelPersonalBudget::getStartDateSelectedPeriod();
        $endDate = ModelPersonalBudget::getEndDateSelectedPeriod();
        $db = static::getDB();
        $queryNameSelectedPeriodExpense = $db->prepare('SELECT 
        ex.amount AS amn,
        ex.date_of_expense AS dateExp,
        pay.name AS pay,
        exCat.name AS excategory,
        ex.expense_comment AS comment,
        ex.id AS exID
        FROM expenses_category_assigned_to_users AS exCat 
        INNER JOIN expenses AS ex ON exCat.id = ex.expense_category_assigned_to_user_id 
        INNER JOIN payment_methods_assigned_to_users AS pay ON ex.payment_method_assigned_to_user_id = pay.id
        WHERE ex.user_id = :userId AND date_of_expense >= :dateSelectedPeriod1 AND date_of_expense <= :dateSelectedPeriod2
        ORDER BY date_of_expense ASC');
        $queryNameSelectedPeriodExpense->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryNameSelectedPeriodExpense->bindValue(':dateSelectedPeriod1', $startDate, PDO::PARAM_STR);
        $queryNameSelectedPeriodExpense->bindValue(':dateSelectedPeriod2', $endDate, PDO::PARAM_STR);
        $queryNameSelectedPeriodExpense->execute();
    
        $queryExpensePeriod = $queryNameSelectedPeriodExpense->fetchAll();

        return $queryExpensePeriod;
    }

    public static function incomesSelectedPeriodSum()
    {
        $startDate = ModelPersonalBudget::getStartDateSelectedPeriod();
        $endDate = ModelPersonalBudget::getEndDateSelectedPeriod();
        $db = static::getDB();
        $querySumIncomes = $db->prepare('SELECT SUM(amount) AS incSum FROM incomes WHERE user_id = :userId AND date_of_income >= :dateSelectedPeriod1 AND date_of_income <= :dateSelectedPeriod2');
        $querySumIncomes->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $querySumIncomes->bindValue(':dateSelectedPeriod1', $startDate, PDO::PARAM_STR);
        $querySumIncomes->bindValue(':dateSelectedPeriod2', $endDate, PDO::PARAM_STR);
        $querySumIncomes->execute();
    
        $incomesSum = $querySumIncomes->fetch();
        
        return $incomesSum;
    }

    public static function expensesSelectedPeriodSum()
    {
        $startDate = ModelPersonalBudget::getStartDateSelectedPeriod();
        $endDate = ModelPersonalBudget::getEndDateSelectedPeriod();
        $db = static::getDB();
        $querySumExpenses = $db->prepare('SELECT SUM(amount) AS expSum FROM expenses WHERE user_id = :userId AND date_of_expense >= :dateSelectedPeriod1 AND date_of_expense <= :dateSelectedPeriod2');
        $querySumExpenses->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $querySumExpenses->bindValue(':dateSelectedPeriod1', $startDate, PDO::PARAM_STR);
        $querySumExpenses->bindValue(':dateSelectedPeriod2', $endDate, PDO::PARAM_STR);
        $querySumExpenses->execute();
    
        $expensesSum = $querySumExpenses->fetch();   
        
        return $expensesSum;
    }

    public static function getQueryNamePaymentMethodsDefault()
    {
        $db = static::getDB();
        $queryNamePaymentMethodsDefault = $db->prepare('SELECT name FROM payment_methods_default');	
        $queryNamePaymentMethodsDefault->execute();

        $queryNamePayment = $queryNamePaymentMethodsDefault->fetchAll();

        return $queryNamePayment;
    }   

    public function inserIncomesIntoIncomesCategoryAssignedToUsers($currentUserId)
    {

        $db = static::getDB();

        $queryIncomesName = static::getQueryIncomesNameDefault();

        foreach ($queryIncomesName as $catName){
            $insertIntoAssignedToUsers = $db->prepare('INSERT INTO incomes_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)');
            $insertIntoAssignedToUsers->bindValue(':user_id', $currentUserId, PDO::PARAM_INT);
            $insertIntoAssignedToUsers->bindValue(':name', "{$catName['name']}", PDO::PARAM_STR);
            $insertIntoAssignedToUsers->execute();
        }
    }

    public function insertExpensesIntoExpensesCategoryAssignedToUsers($currentUserId)
    {

        $db = static::getDB();

        $queryExpensesName = static::getQueryExpensesNameDefault();
        
        foreach ($queryExpensesName as $catExpenseName){
            $insertIntoExpensesCategoryAssignedToUsers = $db->prepare('INSERT INTO expenses_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)');
            $insertIntoExpensesCategoryAssignedToUsers->bindValue(':user_id', $currentUserId, PDO::PARAM_INT);
            $insertIntoExpensesCategoryAssignedToUsers->bindValue(':name', "{$catExpenseName['name']}", PDO::PARAM_STR);
            $insertIntoExpensesCategoryAssignedToUsers->execute();
        }
    }

    public function insertIntoPaymentMethodsAssignedToUsers($currentUserId) 
    {
        $db = static::getDB();

        $queryPaymentName = static::getQueryNamePaymentMethodsDefault();

        foreach ($queryPaymentName as $paymentMethods){
            $insertIntoExpensesCategoryAssignedToUsers = $db->prepare('INSERT INTO payment_methods_assigned_to_users (user_id, name) VALUES (:user_id, :name)');
            $insertIntoExpensesCategoryAssignedToUsers->bindValue(':user_id', $currentUserId, PDO::PARAM_INT);
            $insertIntoExpensesCategoryAssignedToUsers->bindValue(':name', "{$paymentMethods['name']}", PDO::PARAM_STR);
            $insertIntoExpensesCategoryAssignedToUsers->execute();
        }   
    }  

    public function getIdFromIncomesCategoryAssignedToUsers($id)
    {

        $db = static::getDB();
        $queryPaymentCategoryIncome = $db->prepare('SELECT id FROM incomes_category_assigned_to_users WHERE name = :nameIncomeCategory AND user_id = :userId');	
		$queryPaymentCategoryIncome->bindValue(':nameIncomeCategory', $paymentCategoryIncomeName, PDO::PARAM_STR);
		$queryPaymentCategoryIncome->bindValue(':userId', $id, PDO::PARAM_INT);
		$queryPaymentCategoryIncome->execute();

		$paymentCategoryIncomeId  = $queryPaymentCategoryIncome->fetch();

        return $paymentCategoryIncomeId['id'];
    }

    public function getpaymentCategoryIncomeId(){

        $paymentCategoryIncomeName = $_POST['paymentCategoryIncomeName'];
        $db = static::getDB();
        $queryPaymentCategoryIncome = $db->prepare('SELECT id FROM incomes_category_assigned_to_users WHERE name = :nameIncomeCategory AND user_id = :userId');	
        $queryPaymentCategoryIncome->bindValue(':nameIncomeCategory', $paymentCategoryIncomeName, PDO::PARAM_STR);
        $queryPaymentCategoryIncome->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryPaymentCategoryIncome->execute();  
        
        $paymentCategoryIncomeId  = $queryPaymentCategoryIncome->fetch();

        return $paymentCategoryIncomeId['id'];
    }

    public function insertToIncomes($user)
    {

        if (empty($this->errors)){

            // $array = get_object_vars($user);

            $personalBudget = new ModelPersonalBudget($_POST);

            // $user = new User($_POST);

            // $userId = $user->getUserId($array['email']);

            $paymentCategoryIncomeId = $personalBudget->getpaymentCategoryIncomeId();

            $amountIncome = $_POST['amountIncome'];
		    $dateIncome = $_POST['dateIncome'];

		    $commentIncome = $_POST['commentIncome'];

            $db = static::getDB();

            $queryIncome = $db->prepare('INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment) VALUES (:userId, :paymentCategoryIncome, :amount, :dateIncome, :commentIncome)');	
            $queryIncome->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
            $queryIncome->bindValue(':paymentCategoryIncome', $paymentCategoryIncomeId, PDO::PARAM_INT);
            $queryIncome->bindValue(':amount', $amountIncome, PDO::PARAM_STR);
            $queryIncome->bindValue(':dateIncome', $dateIncome, PDO::PARAM_STR);
            $queryIncome->bindValue(':commentIncome', $commentIncome, PDO::PARAM_STR);
    
            return $queryIncome->execute();
        }
        return false;
    }

    public function getPaymentId()
    {
        $db = static::getDB();
        $paymentName = $_POST['paymentMethod'];
        $paymentMethod = $db->prepare('SELECT id FROM payment_methods_assigned_to_users WHERE name = :paymentName AND user_id = :userId');	
		$paymentMethod->bindValue(':paymentName', $paymentName, PDO::PARAM_STR);
		$paymentMethod->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
		$paymentMethod->execute();
	
		$getPaymentId = $paymentMethod->fetch();
        
        return $getPaymentId['id'];
    }

    public function getPaymentCategoryExpenseId()
    {
        $db = static::getDB();
        $paymentCategoryExpense = $_POST['paymentCategoryExpense'];
        $queryPaymentCategoryExpense = $db->prepare('SELECT id FROM expenses_category_assigned_to_users WHERE name = :nameExpCat AND user_id = :userId');	
		$queryPaymentCategoryExpense->bindValue(':nameExpCat', $paymentCategoryExpense, PDO::PARAM_STR);
		$queryPaymentCategoryExpense->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
		$queryPaymentCategoryExpense->execute();

		$paymentCategoryExpenseId  = $queryPaymentCategoryExpense -> fetch();
        
        return $paymentCategoryExpenseId['id'];
    }

    public function insertToExpenses($user)
    {
        // $array = get_object_vars($user);
        $personalBudget = new ModelPersonalBudget($_POST);

        // $user = new User($_POST);
        // $userId = $user->getUserId($array['email']);

        $paymentCatExpenseId = $personalBudget->getpaymentCategoryExpenseId();

        $paymentId = $personalBudget->getPaymentId();

        $db = static::getDB();

        $amountExpense = $_POST['amountExpense'];
        $dateExpense = $_POST['dateExpense'];
        $commentExpense = $_POST['commentExpense'];

        $queryExpense = $db->prepare('INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) VALUES (:userId, :expense_category, :payment_method, :amount, :dateExpense, :commentExpense)');	
		$queryExpense->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
		$queryExpense->bindValue(':expense_category', $paymentCatExpenseId, PDO::PARAM_INT);
		$queryExpense->bindValue(':payment_method', $paymentId, PDO::PARAM_INT);
		$queryExpense->bindValue(':amount', $amountExpense, PDO::PARAM_STR);
		$queryExpense->bindValue(':dateExpense', $dateExpense, PDO::PARAM_STR);
		$queryExpense->bindValue(':commentExpense', $commentExpense, PDO::PARAM_STR);
		
        return $queryExpense->execute();
    }
}
