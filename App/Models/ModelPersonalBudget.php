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
    public $userId;
    public $email;
    public $paymentCategoryIncomeName;
 
    public function getRegisteredUser()
    {

        $queryId = $db->prepare('SELECT id FROM users WHERE name = :userName');	
        $queryId->bindValue(':userName', $nick, PDO::PARAM_STR);
        $queryId->execute();
    
        $userId = $queryId->fetch();
    }

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

    public static function getQueryNameIncome($userId, $dataHelp)
    {
        $db = static::getDB();
        $queryNameIncome = $db->prepare('SELECT * FROM incomes_category_assigned_to_users INNER JOIN incomes ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id WHERE incomes.user_id = :userId AND date_of_income LIKE :dataHelpCurrentMonth ORDER BY date_of_income ASC');
        $queryNameIncome->bindValue(':userId', $userId, PDO::PARAM_INT);
        $queryNameIncome->bindValue(':dataHelpCurrentMonth', $dataHelp, PDO::PARAM_STR);
        $queryNameIncome->execute();

        $queryName = $queryNameIncome->fetchAll();   
        return $queryName;
    }
    public static function getQueryNameExpense($userId, $dataHelp)
    {
        $db = static::getDB();
        $queryNameExpense = $db->prepare('SELECT 
        ex.amount AS amn,
        ex.date_of_expense AS dateExp,
        pay.name AS pay,
        exCat.name AS excategory,
        ex.expense_comment AS comment
        FROM expenses_category_assigned_to_users AS exCat 
        INNER JOIN expenses AS ex ON exCat.id = ex.expense_category_assigned_to_user_id 
        INNER JOIN payment_methods_assigned_to_users AS pay ON ex.payment_method_assigned_to_user_id = pay.id
        WHERE ex.user_id = :userId AND date_of_expense LIKE :dataHelpCurrentMonth 
        ORDER BY date_of_expense ASC');
        $queryNameExpense->bindValue(':userId', $userId, PDO::PARAM_INT);
        $queryNameExpense->bindValue(':dataHelpCurrentMonth', $dataHelp, PDO::PARAM_STR);
        $queryNameExpense->execute();

        $queryExpense = $queryNameExpense->fetchAll();
        
        return $queryExpense;
    }

    public static function incomesSum($userId, $dataHelp)
    {
        $db = static::getDB();
        $querySumIncomes = $db->prepare('SELECT SUM(amount) AS incSum FROM incomes WHERE user_id = :userId AND date_of_income LIKE :dataHelpCurrentMonth');
        $querySumIncomes->bindValue(':userId', $userId, PDO::PARAM_INT);
        $querySumIncomes->bindValue(':dataHelpCurrentMonth', $dataHelp, PDO::PARAM_STR);
        $querySumIncomes->execute();

        $incomesSum = $querySumIncomes->fetch();
        
        return $incomesSum;
    }

    public static function expensesSum($userId, $dataHelp)
    {
        $db = static::getDB();
        $querySumExpenses = $db->prepare('SELECT SUM(amount) AS expSum FROM expenses WHERE user_id = :userId AND date_of_expense LIKE :dataHelpCurrentMonth');
        $querySumExpenses->bindValue(':userId', $userId, PDO::PARAM_INT);
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

    public static function getSelectedPeriodQueryNameIncome($userId)
    {
        $startDate = ModelPersonalBudget::getStartDateSelectedPeriod();
        $endDate = ModelPersonalBudget::getEndDateSelectedPeriod();
        $db = static::getDB();
        $queryNameSelectedPeriod = $db->prepare('SELECT * FROM incomes_category_assigned_to_users INNER JOIN incomes ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id WHERE incomes.user_id = :userId AND date_of_income >= :dateSelectedPeriod1 AND date_of_income <= :dateSelectedPeriod2 ORDER BY date_of_income ASC');
        $queryNameSelectedPeriod->bindValue(':userId', $userId, PDO::PARAM_INT);
        $queryNameSelectedPeriod->bindValue(':dateSelectedPeriod1', $startDate, PDO::PARAM_STR);
        $queryNameSelectedPeriod->bindValue(':dateSelectedPeriod2', $endDate, PDO::PARAM_STR);
        $queryNameSelectedPeriod->execute();

        $queryName = $queryNameSelectedPeriod->fetchAll();

        return $queryName;
    }
    public static function getSelectedPeriodQueryNameExpense($userId)
    {
        $startDate = ModelPersonalBudget::getStartDateSelectedPeriod();
        $endDate = ModelPersonalBudget::getEndDateSelectedPeriod();
        $db = static::getDB();
        $queryNameSelectedPeriodExpense = $db->prepare('SELECT 
        ex.amount AS amn,
        ex.date_of_expense AS dateExp,
        pay.name AS pay,
        exCat.name AS excategory,
        ex.expense_comment AS comment
        FROM expenses_category_assigned_to_users AS exCat 
        INNER JOIN expenses AS ex ON exCat.id = ex.expense_category_assigned_to_user_id 
        INNER JOIN payment_methods_assigned_to_users AS pay ON ex.payment_method_assigned_to_user_id = pay.id
        WHERE ex.user_id = :userId AND date_of_expense >= :dateSelectedPeriod1 AND date_of_expense <= :dateSelectedPeriod2
        ORDER BY date_of_expense ASC');
        $queryNameSelectedPeriodExpense->bindValue(':userId', $userId, PDO::PARAM_INT);
        $queryNameSelectedPeriodExpense->bindValue(':dateSelectedPeriod1', $startDate, PDO::PARAM_STR);
        $queryNameSelectedPeriodExpense->bindValue(':dateSelectedPeriod2', $endDate, PDO::PARAM_STR);
        $queryNameSelectedPeriodExpense->execute();
    
        $queryExpensePeriod = $queryNameSelectedPeriodExpense->fetchAll();

        return $queryExpensePeriod;
    }

    public static function incomesSelectedPeriodSum($userId)
    {
        $startDate = ModelPersonalBudget::getStartDateSelectedPeriod();
        $endDate = ModelPersonalBudget::getEndDateSelectedPeriod();
        $db = static::getDB();
        $querySumIncomes = $db->prepare('SELECT SUM(amount) AS incSum FROM incomes WHERE user_id = :userId AND date_of_income >= :dateSelectedPeriod1 AND date_of_income <= :dateSelectedPeriod2');
        $querySumIncomes->bindValue(':userId', $userId, PDO::PARAM_INT);
        $querySumIncomes->bindValue(':dateSelectedPeriod1', $startDate, PDO::PARAM_STR);
        $querySumIncomes->bindValue(':dateSelectedPeriod2', $endDate, PDO::PARAM_STR);
        $querySumIncomes->execute();
    
        $incomesSum = $querySumIncomes->fetch();
        
        return $incomesSum;
    }

    public static function expensesSelectedPeriodSum($userId)
    {
        $startDate = ModelPersonalBudget::getStartDateSelectedPeriod();
        $endDate = ModelPersonalBudget::getEndDateSelectedPeriod();
        $db = static::getDB();
        $querySumExpenses = $db->prepare('SELECT SUM(amount) AS expSum FROM expenses WHERE user_id = :userId AND date_of_expense >= :dateSelectedPeriod1 AND date_of_expense <= :dateSelectedPeriod2');
        $querySumExpenses->bindValue(':userId', $userId, PDO::PARAM_INT);
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

    public function getpaymentCategoryIncomeId($userId){

        $paymentCategoryIncomeName = $_POST['paymentCategoryIncomeName'];
        $db = static::getDB();
        $queryPaymentCategoryIncome = $db->prepare('SELECT id FROM incomes_category_assigned_to_users WHERE name = :nameIncomeCategory AND user_id = :userId');	
        $queryPaymentCategoryIncome->bindValue(':nameIncomeCategory', $paymentCategoryIncomeName, PDO::PARAM_STR);
        $queryPaymentCategoryIncome->bindValue(':userId', $userId, PDO::PARAM_INT);
        $queryPaymentCategoryIncome->execute();  
        
        $paymentCategoryIncomeId  = $queryPaymentCategoryIncome->fetch();

        return $paymentCategoryIncomeId['id'];
    }

    public function insertToIncomes($user)
    {

        if (empty($this->errors)){

            $array = get_object_vars($user);

            $personalBudget = new ModelPersonalBudget($_POST);

            $user = new User($_POST);

            $userId = $user->getUserId($array['email']);

            $paymentCategoryIncomeId = $personalBudget->getpaymentCategoryIncomeId($userId);

            $amountIncome = $_POST['amountIncome'];
		    $dateIncome = $_POST['dateIncome'];

		    $commentIncome = $_POST['commentIncome'];

            $db = static::getDB();

            $queryIncome = $db->prepare('INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment) VALUES (:userId, :paymentCategoryIncome, :amount, :dateIncome, :commentIncome)');	
            $queryIncome->bindValue(':userId', $userId, PDO::PARAM_INT);
            $queryIncome->bindValue(':paymentCategoryIncome', $paymentCategoryIncomeId, PDO::PARAM_INT);
            $queryIncome->bindValue(':amount', $amountIncome, PDO::PARAM_STR);
            $queryIncome->bindValue(':dateIncome', $dateIncome, PDO::PARAM_STR);
            $queryIncome->bindValue(':commentIncome', $commentIncome, PDO::PARAM_STR);
    
            return $queryIncome->execute();
        }
        return false;
    }

    public function getPaymentId($idOfUser)
    {
        $db = static::getDB();
        $paymentName = $_POST['paymentMethod'];
        $paymentMethod = $db->prepare('SELECT id FROM payment_methods_assigned_to_users WHERE name = :paymentName AND user_id = :userId');	
		$paymentMethod->bindValue(':paymentName', $paymentName, PDO::PARAM_STR);
		$paymentMethod->bindValue(':userId', $idOfUser, PDO::PARAM_INT);
		$paymentMethod->execute();
	
		$getPaymentId = $paymentMethod->fetch();
        
        return $getPaymentId['id'];
    }

    public function getPaymentCategoryExpenseId($idOfUser)
    {
        $db = static::getDB();
        $paymentCategoryExpense = $_POST['paymentCategoryExpense'];
        $queryPaymentCategoryExpense = $db->prepare('SELECT id FROM expenses_category_assigned_to_users WHERE name = :nameExpCat AND user_id = :userId');	
		$queryPaymentCategoryExpense->bindValue(':nameExpCat', $paymentCategoryExpense, PDO::PARAM_STR);
		$queryPaymentCategoryExpense->bindValue(':userId', $idOfUser, PDO::PARAM_INT);
		$queryPaymentCategoryExpense->execute();

		$paymentCategoryExpenseId  = $queryPaymentCategoryExpense -> fetch();
        
        return $paymentCategoryExpenseId['id'];
    }

    public function insertToExpenses($user)
    {
        $array = get_object_vars($user);
        $personalBudget = new ModelPersonalBudget($_POST);

        $user = new User($_POST);
        $userId = $user->getUserId($array['email']);

        $paymentCatExpenseId = $personalBudget->getpaymentCategoryExpenseId($userId);

        $paymentId = $personalBudget->getPaymentId($userId);

        $db = static::getDB();

        $amountExpense = $_POST['amountExpense'];
        $dateExpense = $_POST['dateExpense'];
        $commentExpense = $_POST['commentExpense'];

        $queryExpense = $db->prepare('INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) VALUES (:userId, :expense_category, :payment_method, :amount, :dateExpense, :commentExpense)');	
		$queryExpense->bindValue(':userId', $userId, PDO::PARAM_INT);
		$queryExpense->bindValue(':expense_category', $paymentCatExpenseId, PDO::PARAM_INT);
		$queryExpense->bindValue(':payment_method', $paymentId, PDO::PARAM_INT);
		$queryExpense->bindValue(':amount', $amountExpense, PDO::PARAM_STR);
		$queryExpense->bindValue(':dateExpense', $dateExpense, PDO::PARAM_STR);
		$queryExpense->bindValue(':commentExpense', $commentExpense, PDO::PARAM_STR);
		
        return $queryExpense->execute();
    }
}
