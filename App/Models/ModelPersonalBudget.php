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
    public $email;
    public $paymentCategoryIncomeName;
 
    public function updateIncomes()
    {
        $db = static::getDB();
        $catIncomeId = $this->getpaymentCategoryIncomeId();
        $amountIncome = $_POST['amountIncome'];
        $dateIncome = $_POST['dateIncome'];
        $commentIncome = $_POST['commentIncome'];

        $sql = 'UPDATE incomes 
                SET income_category_assigned_to_user_id  = :income_category,  
                amount = :amount,
                date_of_income = :dateIncome,
                income_comment  = :commentIncome
                WHERE id=:incomeEditId';

        $queryEditIncome = $db->prepare($sql);
		$queryEditIncome->bindValue(':income_category', $catIncomeId, PDO::PARAM_INT);
		$queryEditIncome->bindValue(':amount', $amountIncome, PDO::PARAM_STR);
		$queryEditIncome->bindValue(':dateIncome', $dateIncome, PDO::PARAM_STR);
		$queryEditIncome->bindValue(':commentIncome', $commentIncome, PDO::PARAM_STR);
        $queryEditIncome->bindValue(':incomeEditId', $_SESSION['idIncomesEditRow'], PDO::PARAM_INT);
        
        return $queryEditIncome->execute();
    }

    public function updateExpenses()
    {
        $db = static::getDB();
        $paymentCatExpenseId = $this->getpaymentCategoryExpenseId();
        $paymentId = $this->getPaymentId();
        $amountExpense = $_POST['amountExpense'];
        $dateExpense = $_POST['dateExpense'];
        $commentExpense = $_POST['commentExpense'];

        $sql = 'UPDATE expenses 
                SET expense_category_assigned_to_user_id = :expense_category,  
                payment_method_assigned_to_user_id = :payment_method,
                amount = :amount,
                date_of_expense = :dateExpense,
                expense_comment = :commentExpense
                WHERE id=:expenseEditId';

        $queryEditExpense = $db->prepare($sql);
		$queryEditExpense->bindValue(':expense_category', $paymentCatExpenseId, PDO::PARAM_INT);
		$queryEditExpense->bindValue(':payment_method', $paymentId, PDO::PARAM_INT);
		$queryEditExpense->bindValue(':amount', $amountExpense, PDO::PARAM_STR);
		$queryEditExpense->bindValue(':dateExpense', $dateExpense, PDO::PARAM_STR);
		$queryEditExpense->bindValue(':commentExpense', $commentExpense, PDO::PARAM_STR);
        $queryEditExpense->bindValue(':expenseEditId', $_SESSION['idExpensesEditRow'], PDO::PARAM_INT);
        
        return $queryEditExpense->execute();
    }

    public static function selectAllFromIncomesToEdit($idIncomesEdit)
    {
        $db = static::getDB();
        $sql = 'SELECT 
                inc.amount AS amn,
                inc.date_of_income AS dateInc,
                incCat.name AS incCategory,
                inc.income_comment AS comment,
                inc.id AS incID
                FROM incomes_category_assigned_to_users AS incCat
                INNER JOIN incomes AS inc ON incCat.id = inc.income_category_assigned_to_user_id 
                WHERE inc.id = :id';

        $queryEditIncome = $db->prepare($sql);
        $queryEditIncome->bindValue(':id', $idIncomesEdit, PDO::PARAM_INT);
        $queryEditIncome->execute();

        $queryName = $queryEditIncome->fetchAll();   
        return $queryName;        
    }

    public static function selectAllFromExpensesToEdit($idExpensesEdit)
    {
        $db = static::getDB();

        $sql = 'SELECT 
                ex.amount AS amn,
                ex.date_of_expense AS dateExp,
                pay.name AS pay,
                exCat.name AS excategory,
                ex.expense_comment AS comment,
                ex.id AS exID      
                FROM expenses_category_assigned_to_users AS exCat 
                INNER JOIN expenses AS ex ON exCat.id = ex.expense_category_assigned_to_user_id 
                INNER JOIN payment_methods_assigned_to_users AS pay ON ex.payment_method_assigned_to_user_id = pay.id
                WHERE ex.id = :id';

        $queryEditExpenses = $db->prepare($sql);
        $queryEditExpenses->bindValue(':id', $idExpensesEdit, PDO::PARAM_INT);
        $queryEditExpenses->execute();

        $queryName = $queryEditExpenses->fetchAll();   
        return $queryName;
    }

    public function deleteIncome()
    {
        $db = static::getDB();

        $sql = 'DELETE FROM incomes WHERE id = :idOfRow';

        $queryDeleteIncome = $db->prepare($sql);
        $queryDeleteIncome->bindValue(':idOfRow', $_SESSION['idIncomesDelete'], PDO::PARAM_INT);
        $queryDeleteIncome->execute();

        return $queryDeleteIncome;
    }

    public function deleteExpense()
    {
        $db = static::getDB();

        $sql = 'DELETE FROM expenses WHERE id = :idOfRow';

        $queryDeleteExpense = $db->prepare($sql);
        $queryDeleteExpense->bindValue(':idOfRow', $_SESSION['idExpensesDelete'], PDO::PARAM_INT);
        $queryDeleteExpense->execute();

        return $queryDeleteExpense;
    }

    public static function getQueryIncomesNameDefault()
    {
        $db = static::getDB();

        $sql = 'SELECT name FROM incomes_category_default';

        $queryNameDefault = $db->prepare($sql);	
        $queryNameDefault->execute();

        $queryName = $queryNameDefault->fetchAll();

        return $queryName;
    }

    public static function getQueryExpensesNameDefault()
    {
        $db = static::getDB();

        $sql = 'SELECT name FROM expenses_category_default';

        $queryNameExpenseCategoryDefault = $db->prepare($sql);	
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

    public static function getQueryNameIncome($dataHelp)
    {
        $db = static::getDB();

        $sql = 'SELECT 
                inc.amount AS amn,
                inc.date_of_income AS dateInc,
                incCat.name AS incCategory,
                inc.income_comment AS comment,
                inc.id AS incID
                FROM incomes_category_assigned_to_users AS incCat
                INNER JOIN incomes AS inc ON incCat.id = inc.income_category_assigned_to_user_id 
                WHERE inc.user_id = :userId AND date_of_income LIKE :dataHelp 
                ORDER BY date_of_income DESC';

        $queryNameIncome = $db->prepare($sql);
        $queryNameIncome->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryNameIncome->bindValue(':dataHelp', $dataHelp, PDO::PARAM_STR);
        $queryNameIncome->execute();

        $queryName = $queryNameIncome->fetchAll();   
        return $queryName;
    }

    public static function getQueryNameExpense($dataHelp)
    {
        $db = static::getDB();

        $sql = 'SELECT 
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
                ORDER BY date_of_expense DESC';

        $queryNameExpense = $db->prepare($sql);
        $queryNameExpense->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryNameExpense->bindValue(':dataHelp', $dataHelp, PDO::PARAM_STR);
        $queryNameExpense->execute();

        $queryExpense = $queryNameExpense->fetchAll();
        
        return $queryExpense;
    }

    public static function sumOfNamesFromIncomesToChart($dataHelp)
    {
        $db = static::getDB();

        $sql = 'SELECT 
                income_category_assigned_to_user_id AS inc_assigned_id, 
                SUM(amount) AS incNameSum, 
                inc_cat.name AS catName
                FROM incomes AS inc
                INNER JOIN incomes_category_assigned_to_users AS inc_cat ON inc_cat.id = inc.income_category_assigned_to_user_id
                WHERE inc.user_id = :userId AND date_of_income LIKE :dataHelp
                GROUP BY income_category_assigned_to_user_id
                ORDER BY incNameSum DESC';

        $querySumIncomes = $db->prepare($sql);
        $querySumIncomes->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $querySumIncomes->bindValue(':dataHelp', $dataHelp, PDO::PARAM_STR);
        $querySumIncomes->execute();

        $incomesSumToChart = $querySumIncomes->fetchAll();
        
        return $incomesSumToChart;
    }

    public static function sumOfNamesFromIncomesToChartSelectedPeriod()
    {
        $startDate = self::getStartDateSelectedPeriod();
        $endDate = self::getEndDateSelectedPeriod();
        $db = static::getDB();

        $sql = 'SELECT 
                income_category_assigned_to_user_id AS inc_assigned_id, 
                SUM(amount) AS incNameSum, 
                inc_cat.name AS catName
                FROM incomes AS inc
                INNER JOIN incomes_category_assigned_to_users AS inc_cat ON inc_cat.id = inc.income_category_assigned_to_user_id
                WHERE inc.user_id = :userId AND date_of_income >= :dateSelectedPeriod1 AND date_of_income <= :dateSelectedPeriod2
                GROUP BY income_category_assigned_to_user_id
                ORDER BY incNameSum DESC';

        $querySumIncomes = $db->prepare($sql);
        $querySumIncomes->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $querySumIncomes->bindValue(':dateSelectedPeriod1', $startDate, PDO::PARAM_STR);
        $querySumIncomes->bindValue(':dateSelectedPeriod2', $endDate, PDO::PARAM_STR);
        $querySumIncomes->execute();

        $incomesSumToChart = $querySumIncomes->fetchAll();
        
        return $incomesSumToChart;
    }    

    public static function sumOfNamesFromExpensesToChart($dataHelp)
    {
        $db = static::getDB();

        $sql = 'SELECT 
                expense_category_assigned_to_user_id AS exp_assigned_id, 
                SUM(amount) AS expNameSum, 
                exp_cat.name AS catName
                FROM expenses AS exp
                INNER JOIN expenses_category_assigned_to_users AS exp_cat ON exp_cat.id = exp.expense_category_assigned_to_user_id
                WHERE exp.user_id = :userId AND date_of_expense LIKE :dataHelp
                GROUP BY expense_category_assigned_to_user_id
                ORDER BY expNameSum DESC';

        $querySumExpenses = $db->prepare($sql);
        $querySumExpenses->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $querySumExpenses->bindValue(':dataHelp', $dataHelp, PDO::PARAM_STR);
        $querySumExpenses->execute();

        $expensesSumToChart = $querySumExpenses->fetchAll();
        
        return $expensesSumToChart;
    }

    public static function sumOfNamesFromExpensesToChartSelectedPeriod()
    {
        $startDate = self::getStartDateSelectedPeriod();
        $endDate = self::getEndDateSelectedPeriod();
        $db = static::getDB();

        $sql = 'SELECT 
                expense_category_assigned_to_user_id AS exp_assigned_id, 
                SUM(amount) AS expNameSum, 
                exp_cat.name AS catName
                FROM expenses AS exp
                INNER JOIN expenses_category_assigned_to_users AS exp_cat ON exp_cat.id = exp.expense_category_assigned_to_user_id
                WHERE exp.user_id = :userId AND date_of_expense >= :dateSelectedPeriod1 AND date_of_expense <= :dateSelectedPeriod2
                GROUP BY expense_category_assigned_to_user_id
                ORDER BY expNameSum DESC';

        $querySumExpenses = $db->prepare($sql);
        $querySumExpenses->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $querySumExpenses->bindValue(':dateSelectedPeriod1', $startDate, PDO::PARAM_STR);
        $querySumExpenses->bindValue(':dateSelectedPeriod2', $endDate, PDO::PARAM_STR);
        $querySumExpenses->execute();

        $expensesSumToChart = $querySumExpenses->fetchAll();
        
        return $expensesSumToChart;
    }

    public static function incomesSum($dataHelp)
    {
        $db = static::getDB();

        $sql = 'SELECT SUM(amount) AS incSum FROM incomes WHERE user_id = :userId AND date_of_income LIKE :dataHelpCurrentMonth';

        $querySumIncomes = $db->prepare($sql);
        $querySumIncomes->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $querySumIncomes->bindValue(':dataHelpCurrentMonth', $dataHelp, PDO::PARAM_STR);
        $querySumIncomes->execute();

        $incomesSum = $querySumIncomes->fetch();
        
        return $incomesSum;
    }

    public static function expensesSum($dataHelp)
    {
        $db = static::getDB();

        $sql = 'SELECT SUM(amount) AS expSum FROM expenses WHERE user_id = :userId AND date_of_expense LIKE :dataHelpCurrentMonth';

        $querySumExpenses = $db->prepare($sql);
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
        $startDate = self::getStartDateSelectedPeriod();
        $endDate = self::getEndDateSelectedPeriod();
        $db = static::getDB();

        $sql = 'SELECT 
                inc.amount AS amn,
                inc.date_of_income AS dateInc,
                incCat.name AS incCategory,
                inc.income_comment AS comment,
                inc.id AS incID
                FROM incomes_category_assigned_to_users AS incCat
                INNER JOIN incomes AS inc ON incCat.id = inc.income_category_assigned_to_user_id WHERE inc.user_id = :userId AND date_of_income >= :dateSelectedPeriod1 AND date_of_income <= :dateSelectedPeriod2 ORDER BY date_of_income DESC';

        $queryNameSelectedPeriod = $db->prepare($sql);
        $queryNameSelectedPeriod->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryNameSelectedPeriod->bindValue(':dateSelectedPeriod1', $startDate, PDO::PARAM_STR);
        $queryNameSelectedPeriod->bindValue(':dateSelectedPeriod2', $endDate, PDO::PARAM_STR);
        $queryNameSelectedPeriod->execute();

        $queryName = $queryNameSelectedPeriod->fetchAll();

        return $queryName;
    }
    public static function getSelectedPeriodQueryNameExpense()
    {
        $startDate = self::getStartDateSelectedPeriod();
        $endDate = self::getEndDateSelectedPeriod();
        $db = static::getDB();

        $sql = 'SELECT 
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
                ORDER BY date_of_expense DESC';

        $queryNameSelectedPeriodExpense = $db->prepare($sql);
        $queryNameSelectedPeriodExpense->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryNameSelectedPeriodExpense->bindValue(':dateSelectedPeriod1', $startDate, PDO::PARAM_STR);
        $queryNameSelectedPeriodExpense->bindValue(':dateSelectedPeriod2', $endDate, PDO::PARAM_STR);
        $queryNameSelectedPeriodExpense->execute();
    
        $queryExpensePeriod = $queryNameSelectedPeriodExpense->fetchAll();

        return $queryExpensePeriod;
    }

    public static function incomesSelectedPeriodSum()
    {
        $startDate = self::getStartDateSelectedPeriod();
        $endDate = self::getEndDateSelectedPeriod();
        $db = static::getDB();

        $sql = 'SELECT SUM(amount) AS incSum FROM incomes WHERE user_id = :userId AND date_of_income >= :dateSelectedPeriod1 AND date_of_income <= :dateSelectedPeriod2';

        $querySumIncomes = $db->prepare($sql);
        $querySumIncomes->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $querySumIncomes->bindValue(':dateSelectedPeriod1', $startDate, PDO::PARAM_STR);
        $querySumIncomes->bindValue(':dateSelectedPeriod2', $endDate, PDO::PARAM_STR);
        $querySumIncomes->execute();
    
        $incomesSum = $querySumIncomes->fetch();
        
        return $incomesSum;
    }

    public static function expensesSelectedPeriodSum()
    {
        $startDate = self::getStartDateSelectedPeriod();
        $endDate = self::getEndDateSelectedPeriod();
        $db = static::getDB();

        $sql = 'SELECT SUM(amount) AS expSum FROM expenses WHERE user_id = :userId AND date_of_expense >= :dateSelectedPeriod1 AND date_of_expense <= :dateSelectedPeriod2';

        $querySumExpenses = $db->prepare($sql);
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

        $sql = 'SELECT name FROM payment_methods_default';

        $queryNamePaymentMethodsDefault = $db->prepare($sql);	
        $queryNamePaymentMethodsDefault->execute();

        $queryNamePayment = $queryNamePaymentMethodsDefault->fetchAll();

        return $queryNamePayment;
    }   

    public function inserIncomesIntoIncomesCategoryAssignedToUsers($currentUserId)
    {

        $db = static::getDB();

        $queryIncomesName = static::getQueryIncomesNameDefault();

        foreach ($queryIncomesName as $catName){

            $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)';

            $insertIntoAssignedToUsers = $db->prepare($sql);
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

            $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)';

            $insertIntoExpensesCategoryAssignedToUsers = $db->prepare($sql);
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

            $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name) VALUES (:user_id, :name)';

            $insertIntoExpensesCategoryAssignedToUsers = $db->prepare($sql);
            $insertIntoExpensesCategoryAssignedToUsers->bindValue(':user_id', $currentUserId, PDO::PARAM_INT);
            $insertIntoExpensesCategoryAssignedToUsers->bindValue(':name', "{$paymentMethods['name']}", PDO::PARAM_STR);
            $insertIntoExpensesCategoryAssignedToUsers->execute();
        }   
    }  

    public function getIdFromIncomesCategoryAssignedToUsers($id)
    {

        $db = static::getDB();

        $sql = 'SELECT id FROM incomes_category_assigned_to_users WHERE name = :nameIncomeCategory AND user_id = :userId';

        $queryPaymentCategoryIncome = $db->prepare($sql);	
		$queryPaymentCategoryIncome->bindValue(':nameIncomeCategory', $paymentCategoryIncomeName, PDO::PARAM_STR);
		$queryPaymentCategoryIncome->bindValue(':userId', $id, PDO::PARAM_INT);
		$queryPaymentCategoryIncome->execute();

		$paymentCategoryIncomeId  = $queryPaymentCategoryIncome->fetch();

        return $paymentCategoryIncomeId['id'];
    }

    public function getpaymentCategoryIncomeId(){

        $paymentCategoryIncomeName = $_POST['paymentCategoryIncomeName'];
        $db = static::getDB();

        $sql = 'SELECT id FROM incomes_category_assigned_to_users WHERE name = :nameIncomeCategory AND user_id = :userId';

        $queryPaymentCategoryIncome = $db->prepare($sql);	
        $queryPaymentCategoryIncome->bindValue(':nameIncomeCategory', $paymentCategoryIncomeName, PDO::PARAM_STR);
        $queryPaymentCategoryIncome->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryPaymentCategoryIncome->execute();  
        
        $paymentCategoryIncomeId  = $queryPaymentCategoryIncome->fetch();

        return $paymentCategoryIncomeId['id'];
    }

    public function insertToIncomes($user)
    {

        if (empty($this->errors)){

            $paymentCategoryIncomeId = $this->getpaymentCategoryIncomeId();

            $amountIncome = $_POST['amountIncome'];
		    $dateIncome = $_POST['dateIncome'];

		    $commentIncome = $_POST['commentIncome'];

            $db = static::getDB();

            $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment) VALUES (:userId, :paymentCategoryIncome, :amount, :dateIncome, :commentIncome)';

            $queryIncome = $db->prepare($sql);	
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

        $sql = 'SELECT id FROM payment_methods_assigned_to_users WHERE name = :paymentName AND user_id = :userId';

        $paymentMethod = $db->prepare($sql);	
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

        $sql = 'SELECT id FROM expenses_category_assigned_to_users WHERE name = :nameExpCat AND user_id = :userId';

        $queryPaymentCategoryExpense = $db->prepare($sql);	
		$queryPaymentCategoryExpense->bindValue(':nameExpCat', $paymentCategoryExpense, PDO::PARAM_STR);
		$queryPaymentCategoryExpense->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
		$queryPaymentCategoryExpense->execute();

		$paymentCategoryExpenseId  = $queryPaymentCategoryExpense -> fetch();

        return $paymentCategoryExpenseId['id'];
    }

    public function insertToExpenses($user)
    {
        $paymentCatExpenseId = $this->getpaymentCategoryExpenseId();

        $paymentId = $this->getPaymentId();

        $db = static::getDB();

        $amountExpense = $_POST['amountExpense'];
        $dateExpense = $_POST['dateExpense'];
        $commentExpense = $_POST['commentExpense'];

        $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) VALUES (:userId, :expense_category, :payment_method, :amount, :dateExpense, :commentExpense)';

        $queryExpense = $db->prepare($sql);	
		$queryExpense->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
		$queryExpense->bindValue(':expense_category', $paymentCatExpenseId, PDO::PARAM_INT);
		$queryExpense->bindValue(':payment_method', $paymentId, PDO::PARAM_INT);
		$queryExpense->bindValue(':amount', $amountExpense, PDO::PARAM_STR);
		$queryExpense->bindValue(':dateExpense', $dateExpense, PDO::PARAM_STR);
		$queryExpense->bindValue(':commentExpense', $commentExpense, PDO::PARAM_STR);
		
        return $queryExpense->execute();
    }
    public static function selectOptionsForIncomes()
    {
        $db = static::getDB();
        $sql = 'SELECT 
                id, name
                FROM incomes_category_assigned_to_users AS incCat
                WHERE incCat.user_id = :user_id';

        $queryOptionIncome = $db->prepare($sql);
        $queryOptionIncome->bindValue(':user_id', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryOptionIncome->execute();

        $queryName = $queryOptionIncome->fetchAll();   
        return $queryName;        
    }
    public static function selectOptionsForExpensesPaymentMethod()
    {
        $db = static::getDB();
        $sql = 'SELECT 
                id, name
                FROM payment_methods_assigned_to_users AS payMeth
                WHERE payMeth.user_id = :user_id';

        $queryOptionPaymentMethodExpense = $db->prepare($sql);
        $queryOptionPaymentMethodExpense->bindValue(':user_id', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryOptionPaymentMethodExpense->execute();

        $queryName = $queryOptionPaymentMethodExpense->fetchAll();   
        return $queryName;        
    }
    public static function selectOptionsForExpensesCategory()
    {
        $db = static::getDB();
        $sql = 'SELECT 
                id, name
                FROM expenses_category_assigned_to_users AS expCat
                WHERE expCat.user_id = :user_id';

        $queryOptionCategoryExpense = $db->prepare($sql);
        $queryOptionCategoryExpense->bindValue(':user_id', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryOptionCategoryExpense->execute();

        $queryName = $queryOptionCategoryExpense->fetchAll();   
        return $queryName;        
    }

    public static function selectNameFromIncomesCategoryToEdit()
    {
        $db = static::getDB();
        $sql = 'SELECT 
                name
                FROM incomes_category_assigned_to_users
                WHERE id = :id';

        $queryEditIncome = $db->prepare($sql);
        $queryEditIncome->bindValue(':id', $_SESSION['incomesCatID'], PDO::PARAM_INT);
        $queryEditIncome->execute();

        $nameOfIncomeCategory  = $queryEditIncome -> fetch(); 

        return $nameOfIncomeCategory['name'];        
    }

    public static function selectNameFromExpensesCategoryToEdit()
    {
        $db = static::getDB();
        $sql = 'SELECT 
                name
                FROM expenses_category_assigned_to_users
                WHERE id = :id';

        $queryEditExpense = $db->prepare($sql);
        $queryEditExpense->bindValue(':id', $_SESSION['expensesCatID'], PDO::PARAM_INT);
        $queryEditExpense->execute();
        $nameOfExpenseCategory  = $queryEditExpense -> fetch(); 

        return $nameOfExpenseCategory['name'];        
    }

    public static function selectNameFromPayMethCategoryToEdit()
    {
        $db = static::getDB();
        $sql = 'SELECT 
                name
                FROM payment_methods_assigned_to_users
                WHERE id = :id';

        $queryEditPayMeth = $db->prepare($sql);
        $queryEditPayMeth->bindValue(':id', $_SESSION['payMethCatID'], PDO::PARAM_INT);
        $queryEditPayMeth->execute();

        $nameOfPayMethCategory  = $queryEditPayMeth -> fetch(); 

        return $nameOfPayMethCategory['name'];        
    }

    public static function selectNameFromIncomesCategoryToDelete()
    {
        $db = static::getDB();
        $sql = 'SELECT 
                name
                FROM incomes_category_assigned_to_users
                WHERE id = :id';

        $queryDeleteIncome = $db->prepare($sql);
        $queryDeleteIncome->bindValue(':id', $_SESSION['idIncomesDeleteCat'], PDO::PARAM_INT);
        $queryDeleteIncome->execute();

        $nameOfIncomeCategory  = $queryDeleteIncome -> fetch(); 

        return $nameOfIncomeCategory['name'];        
    }

    public static function selectNameFromExpensesCategoryToDelete()
    {
        $db = static::getDB();
        $sql = 'SELECT 
                name
                FROM expenses_category_assigned_to_users
                WHERE id = :id';

        $queryDeleteExpense = $db->prepare($sql);
        $queryDeleteExpense->bindValue(':id', $_SESSION['idExpensesDeleteCat'], PDO::PARAM_INT);
        $queryDeleteExpense->execute();

        $nameOfExpenseCategory  = $queryDeleteExpense -> fetch(); 

        return $nameOfExpenseCategory['name'];        
    }

    public static function selectNameFromPayMethCategoryToDelete()
    {
        $db = static::getDB();
        $sql = 'SELECT 
                name
                FROM payment_methods_assigned_to_users
                WHERE id = :id';

        $queryDeletePayMeth = $db->prepare($sql);
        $queryDeletePayMeth->bindValue(':id', $_SESSION['idPayMethDeleteCat'], PDO::PARAM_INT);
        $queryDeletePayMeth->execute();

        $nameOfPayMethCategory  = $queryDeletePayMeth -> fetch(); 

        return $nameOfPayMethCategory['name'];        
    }

    public function editIncomesCategory($editIncomeCategoryName)
    {
        $db = static::getDB();

        $sql = 'UPDATE incomes_category_assigned_to_users 
                SET name  = :income_category
                WHERE id=:incomeCategoryEditId';

        $queryEditIncome = $db->prepare($sql);
		$queryEditIncome->bindValue(':income_category', $editIncomeCategoryName, PDO::PARAM_STR);
        $queryEditIncome->bindValue(':incomeCategoryEditId', $_SESSION['incomesCatID'], PDO::PARAM_INT);
        
        return $queryEditIncome->execute();
    }

    public function editExpensesCategory($editExpenseCategoryName)
    {
        $db = static::getDB();

        $sql = 'UPDATE expenses_category_assigned_to_users 
                SET name  = :expense_category
                WHERE id=:expenseCategoryEditId';

        $queryEditExpense = $db->prepare($sql);
		$queryEditExpense->bindValue(':expense_category', $editExpenseCategoryName, PDO::PARAM_STR);
        $queryEditExpense->bindValue(':expenseCategoryEditId', $_SESSION['expensesCatID'], PDO::PARAM_INT);
        
        return $queryEditExpense->execute();
    }

    public function editPayMethCategory($editPayMethCategoryName)
    {
        $db = static::getDB();

        $sql = 'UPDATE payment_methods_assigned_to_users 
                SET name  = :pay_meth_category
                WHERE id=:payMethCategoryEditId';

        $queryEditPayment = $db->prepare($sql);
		$queryEditPayment->bindValue(':pay_meth_category', $editPayMethCategoryName, PDO::PARAM_STR);
        $queryEditPayment->bindValue(':payMethCategoryEditId', $_SESSION['payMethCatID'], PDO::PARAM_INT);
        
        return $queryEditPayment->execute();
    }

    public function deleteIncomesCategory()
    {
        $db = static::getDB();

        $sql = 'DELETE FROM incomes_category_assigned_to_users WHERE id = :idOfIncomeCategory';

        $queryDeleteIncomeCategory = $db->prepare($sql);
        $queryDeleteIncomeCategory->bindValue(':idOfIncomeCategory', $_SESSION['idIncomesDeleteCat'], PDO::PARAM_INT);
        $queryDeleteIncomeCategory->execute();

        return $queryDeleteIncomeCategory;
    } 

    public function deleteExpensesCategory()
    {
        $db = static::getDB();

        $sql = 'DELETE FROM expenses_category_assigned_to_users WHERE id = :idOfExpenseCategory';

        $queryDeleteIncomeCategory = $db->prepare($sql);
        $queryDeleteIncomeCategory->bindValue(':idOfExpenseCategory', $_SESSION['idExpensesDeleteCat'], PDO::PARAM_INT);
        $queryDeleteIncomeCategory->execute();

        return $queryDeleteIncomeCategory;
    } 

    public function deletePayMethCategory()
    {
        $db = static::getDB();

        $sql = 'DELETE FROM payment_methods_assigned_to_users WHERE id = :idOfPayMethCategory';

        $queryDeleteIncomeCategory = $db->prepare($sql);
        $queryDeleteIncomeCategory->bindValue(':idOfPayMethCategory', $_SESSION['idPayMethDeleteCat'], PDO::PARAM_INT);
        $queryDeleteIncomeCategory->execute();

        return $queryDeleteIncomeCategory;
    }

    public function deleteIncomesRowRelatedToIncomesCatAssignedToUserId()
    {
        $db = static::getDB();

        $sql = 'DELETE FROM incomes WHERE income_category_assigned_to_user_id = :idOfIncomeCategory';

        $queryDeleteIncomesRowRelatedToIncCatAssignedToUser = $db->prepare($sql);
        $queryDeleteIncomesRowRelatedToIncCatAssignedToUser->bindValue(':idOfIncomeCategory', $_SESSION['idIncomesDeleteCat'], PDO::PARAM_INT);
        $queryDeleteIncomesRowRelatedToIncCatAssignedToUser->execute();

        return $queryDeleteIncomesRowRelatedToIncCatAssignedToUser;
    } 

    public function deleteExpensesRowRelatedToExpensesCatAssignedToUserId()
    {
        $db = static::getDB();

        $sql = 'DELETE FROM expenses WHERE expense_category_assigned_to_user_id = :idOfExpenseCategory';

        $queryDeleteExpensesRowRelatedToExpCatAssignedToUser = $db->prepare($sql);
        $queryDeleteExpensesRowRelatedToExpCatAssignedToUser->bindValue(':idOfExpenseCategory', $_SESSION['idExpensesDeleteCat'], PDO::PARAM_INT);
        $queryDeleteExpensesRowRelatedToExpCatAssignedToUser->execute();

        return $queryDeleteExpensesRowRelatedToExpCatAssignedToUser;
    }

    public function deleteExpensesRowRelatedToPayMethCatAssignedToUserId()
    {
        $db = static::getDB();

        $sql = 'DELETE FROM expenses WHERE payment_method_assigned_to_user_id = :idOfPayMethCategory';

        $queryDeleteExpensesRowRelatedToPayMethCatAssignedToUser = $db->prepare($sql);
        $queryDeleteExpensesRowRelatedToPayMethCatAssignedToUser->bindValue(':idOfPayMethCategory', $_SESSION['idPayMethDeleteCat'], PDO::PARAM_INT);
        $queryDeleteExpensesRowRelatedToPayMethCatAssignedToUser->execute();

        return $queryDeleteExpensesRowRelatedToPayMethCatAssignedToUser;
    }

    public function addNewIncomesCategory($newIncomeCat)
    {
        $db = static::getDB();

            $sql = 'INSERT INTO incomes_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)';

            $addNewIncomesCategory = $db->prepare($sql);
            $addNewIncomesCategory->bindValue(':user_id', $_SESSION['userIdSession'], PDO::PARAM_INT);
            $addNewIncomesCategory->bindValue(':name', $newIncomeCat, PDO::PARAM_STR);
            
        return $addNewIncomesCategory->execute();   
    }

    public function addNewExpensesCategory($newExpenseCat)
    {
        $db = static::getDB();

            $sql = 'INSERT INTO expenses_category_assigned_to_users (user_id, name) VALUES (:user_id, :name)';

            $addNewExpensesCategory = $db->prepare($sql);
            $addNewExpensesCategory->bindValue(':user_id', $_SESSION['userIdSession'], PDO::PARAM_INT);
            $addNewExpensesCategory->bindValue(':name', $newExpenseCat, PDO::PARAM_STR);
            
        return $addNewExpensesCategory->execute();   
    }

    public function addNewPayMethCategory($newPayMethCat)
    {
        $db = static::getDB();

            $sql = 'INSERT INTO payment_methods_assigned_to_users (user_id, name) VALUES (:user_id, :name)';

            $addNewPayMethCategory = $db->prepare($sql);
            $addNewPayMethCategory->bindValue(':user_id', $_SESSION['userIdSession'], PDO::PARAM_INT);
            $addNewPayMethCategory->bindValue(':name', $newPayMethCat, PDO::PARAM_STR);
            
        return $addNewPayMethCategory->execute();   
    }

    public function deleteFromDataBaseUser($id)
    {
        $db = static::getDB();

        $sql = 'DELETE FROM users WHERE id = :idOfUser';

        $queryDeleteUser = $db->prepare($sql);
        $queryDeleteUser->bindValue(':idOfUser', $id, PDO::PARAM_INT);
        $queryDeleteUser->execute();

        return $queryDeleteUser;
    }

    public function deleteFromDataBaseIncomesUserID($id)
    {
        $db = static::getDB();

        $sql = 'DELETE FROM incomes WHERE user_id = :idOfUser';

        $queryDeleteIncomesUser = $db->prepare($sql);
        $queryDeleteIncomesUser->bindValue(':idOfUser', $id, PDO::PARAM_INT);
        $queryDeleteIncomesUser->execute();

        return $queryDeleteIncomesUser;
    }

    public function deleteFromDataBaseExpensesUserID($id)
    {
        $db = static::getDB();

        $sql = 'DELETE FROM expenses WHERE user_id = :idOfUser';

        $queryDeleteExpensesUser = $db->prepare($sql);
        $queryDeleteExpensesUser->bindValue(':idOfUser', $id, PDO::PARAM_INT);
        $queryDeleteExpensesUser->execute();

        return $queryDeleteExpensesUser;
    }

    public function deleteFromDataBaseIncomesCategoryAssignedToUser($id)
    {
        $db = static::getDB();

        $sql = 'DELETE FROM incomes_category_assigned_to_users WHERE user_id = :idOfUser';

        $queryDeleteIncAssignedToUser = $db->prepare($sql);
        $queryDeleteIncAssignedToUser->bindValue(':idOfUser', $id, PDO::PARAM_INT);
        $queryDeleteIncAssignedToUser->execute();

        return $queryDeleteIncAssignedToUser;
    }

    public function deleteFromDataBaseExpensesCategoryAssignedToUser($id)
    {
        $db = static::getDB();

        $sql = 'DELETE FROM expenses_category_assigned_to_users WHERE user_id = :idOfUser';

        $queryDeleteExpAssignedToUser = $db->prepare($sql);
        $queryDeleteExpAssignedToUser->bindValue(':idOfUser', $id, PDO::PARAM_INT);
        $queryDeleteExpAssignedToUser->execute();

        return $queryDeleteExpAssignedToUser;
    }
    
    public function deleteFromDataBasePaymentMethodsCategoryAssignedToUser($id)
    {
        $db = static::getDB();

        $sql = 'DELETE FROM payment_methods_assigned_to_users WHERE user_id = :idOfUser';

        $queryDeletePayMethAssignedToUser = $db->prepare($sql);
        $queryDeletePayMethAssignedToUser->bindValue(':idOfUser', $id, PDO::PARAM_INT);
        $queryDeletePayMethAssignedToUser->execute();

        return $queryDeletePayMethAssignedToUser;
    }
}