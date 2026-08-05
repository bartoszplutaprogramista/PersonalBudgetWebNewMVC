<?php

namespace App\Models;

use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;
use DateTime;

#[\AllowDynamicProperties]
class ModelPersonalBudget extends \Core\Model
{

    public $amountIncome;
    public $dateIncome;
    public $commentIncome;
    public $email;
    public $paymentCategoryIncomeName;


    public $errors = [];
    private $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }
 
    public function validateIncomes()
    {
        // $idOfEditedIncome = filter_var($this->data['idOfEditedIncome'], FILTER_VALIDATE_INT);
        // // echo $idOfEditedIncome;
        // // exit;

        // if (!$idOfEditedIncome) {
        //     $this->errors[] = 'Nieprawidłowe id';
        // }
        // KWOTA
        $amount = filter_var($this->data['amountIncome'] ?? null, FILTER_VALIDATE_FLOAT);
        if ($amount === false || $amount <= 0 || $amount > 999999.99) {
            $this->errors[] = 'Nieprawidłowa kwota';
        }

        // DATA
        $date = $this->data['dateIncome'] ?? '';
        $d = DateTime::createFromFormat('Y-m-d', $date);
        if (!$d || $d->format('Y-m-d') !== $date) {
            $this->errors[] = 'Nieprawidłowy format daty';
        }

        // KOMENTARZ
        $this->data['commentIncome'] = mb_substr(trim($this->data['commentIncome'] ?? ''), 0, 100);

        return empty($this->errors);
    }


    public function updateIncomes()
    {

        if (!$this->validateIncomes()) {
            return $this->errors;
        }

                    // $amountIncome = $_POST['amountIncome'];
                    // $dateIncome = $_POST['dateIncome'];
                    // $commentIncome = $_POST['commentIncome'];
        // $paymentCategoryIncomeName = $_POST['paymentCategoryIncomeName'];

        $idIncomeEdited = filter_var($this->data['idOfEditedIncome'], FILTER_VALIDATE_INT);
        if (!$idIncomeEdited) {
            $this->errors[] = 'Błędne ID przychodu';
            return $this->errors;
        }

        $db = static::getDB();

        // $selectCatIncomeId = filter_var($this->data['paymentCategoryIncomeName'], FILTER_VALIDATE_INT);
        // if (!$selectCatIncomeId) {
        //     $this->errors[] = 'Błędne ID kategorii przychodu';
        //     return $this->errors;
        // }
        $catIncomeId = $this->getpaymentCategoryIncomeId($this->data['paymentCategoryIncomeName']);
        // $amountIncome = $_POST['amountIncome'];
        // $dateIncome = $_POST['dateIncome'];
        // $commentIncome = $_POST['commentIncome'];

        $sql = 'UPDATE incomes 
                SET income_category_assigned_to_user_id  = :income_category,  
                amount = :amount,
                date_of_income = :dateIncome,
                income_comment  = :commentIncome
                WHERE id=:incomeEditId
                AND user_id = :userId';

        $queryEditIncome = $db->prepare($sql);
		$queryEditIncome->bindValue(':income_category', $catIncomeId, PDO::PARAM_INT);
		$queryEditIncome->bindValue(':amount', $this->data['amountIncome'], PDO::PARAM_STR);
		$queryEditIncome->bindValue(':dateIncome', $this->data['dateIncome'], PDO::PARAM_STR);
		$queryEditIncome->bindValue(':commentIncome', $this->data['commentIncome'], PDO::PARAM_STR);
        // $queryEditIncome->bindValue(':incomeEditId', $_SESSION['idIncomesEditRow'], PDO::PARAM_INT);
        $queryEditIncome->bindValue(':incomeEditId', $idIncomeEdited, PDO::PARAM_INT);
        $queryEditIncome->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        
        return $queryEditIncome->execute();
    }

    // public function updateExpenses($idExpenseEdited, $amountExpense, $dateExpense, $commentExpense, $paymentName, $paymentCategoryExpense)
    public function validateExpenses()
    {
        // $amountExpense = filter_input(INPUT_POST, 'amountExpense', FILTER_VALIDATE_FLOAT);
        // if ($amountExpense === false || $amountExpense <= 0 || $amountExpense > 999999.99) {
        //     $errors[] = 'Nieprawidłowa kwota';
        // }

        // $dateExpense = $_POST['dateExpense'] ?? '';
        // $d = DateTime::createFromFormat('Y-m-d', $dateExpense);
        // if (!$d || $d->format('Y-m-d') !== $dateExpense) {
        //     $errors[] = 'Nieprawidłowy format daty';
        // }

        // $commentExpense = mb_substr(trim($_POST['commentExpense'] ?? ''), 0, 100);
        
        
        // KWOTA
        $amountExpense = filter_var($this->data['amountExpense'] ?? null, FILTER_VALIDATE_FLOAT);
        if ($amountExpense === false || $amountExpense <= 0 || $amountExpense > 999999.99) {
            $this->errors[] = 'Nieprawidłowa kwota';
        }

        // DATA
        $dateExpense = $this->data['dateExpense'] ?? '';
        $d = DateTime::createFromFormat('Y-m-d', $dateExpense);
        if (!$d || $d->format('Y-m-d') !== $dateExpense) {
            $this->errors[] = 'Nieprawidłowy format daty';
        }

        // KOMENTARZ
        $this->data['commentExpense'] = mb_substr(trim($this->data['commentExpense'] ?? ''), 0, 100);

        return empty($this->errors);
    }

    public function updateExpenses()
    {
        if (!$this->validateExpenses()) {
            return $this->errors;
        }
        $idExpenseEdited = filter_var($this->data['idOfEditedExpense'], FILTER_VALIDATE_INT);
        if (!$idExpenseEdited) {
            $this->errors[] = 'Błędne ID przychodu';
            return $this->errors;
        }
        $db = static::getDB();
        $catExpenseId = $this->getpaymentCategoryExpenseId($this->data['paymentCategoryExpense']);
        $paymentId = $this->getPaymentId($this->data['paymentMethod']);
        // $paymentCatExpenseId = $this->getpaymentCategoryExpenseId($paymentCategoryExpense);
        // $paymentId = $this->getPaymentId($paymentName);
        // $amountExpense = $_POST['amountExpense'];
        // $dateExpense = $_POST['dateExpense'];
        // $commentExpense = $_POST['commentExpense'];

        $sql = 'UPDATE expenses 
                SET expense_category_assigned_to_user_id = :expense_category,  
                payment_method_assigned_to_user_id = :payment_method,
                amount = :amount,
                date_of_expense = :dateExpense,
                expense_comment = :commentExpense
                WHERE id=:expenseEditId
                AND user_id = :userId';

        $queryEditExpense = $db->prepare($sql);
		$queryEditExpense->bindValue(':expense_category', $catExpenseId, PDO::PARAM_INT);
		$queryEditExpense->bindValue(':payment_method', $paymentId, PDO::PARAM_INT);
		$queryEditExpense->bindValue(':amount', $this->data['amountExpense'], PDO::PARAM_STR);
		$queryEditExpense->bindValue(':dateExpense', $this->data['dateExpense'], PDO::PARAM_STR);
		$queryEditExpense->bindValue(':commentExpense', $this->data['commentExpense'], PDO::PARAM_STR);
        // $queryEditExpense->bindValue(':expenseEditId', $_SESSION['idExpensesEditRow'], PDO::PARAM_INT);
        $queryEditExpense->bindValue(':expenseEditId', $idExpenseEdited, PDO::PARAM_INT);
        $queryEditExpense->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        
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
                WHERE inc.id = :id
                AND inc.user_id = :userId';

        $queryEditIncome = $db->prepare($sql);
        $queryEditIncome->bindValue(':id', $idIncomesEdit, PDO::PARAM_INT);
        $queryEditIncome->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryEditIncome->execute();

        $queryName = $queryEditIncome->fetch();   
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
                WHERE ex.id = :id
                AND ex.user_id = :userId';

        $queryEditExpenses = $db->prepare($sql);
        $queryEditExpenses->bindValue(':id', $idExpensesEdit, PDO::PARAM_INT);
        $queryEditExpenses->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryEditExpenses->execute();

        // $queryName = $queryEditExpenses->fetchAll();   
        $queryName = $queryEditExpenses->fetch();   
        return $queryName;
    }

    public function deleteIncome($idIncomesDelete)
    {
        $db = static::getDB();

        $sql = 'DELETE FROM incomes 
                WHERE id = :idOfRow
                AND user_id = :userId';

        $queryDeleteIncome = $db->prepare($sql);
        // $queryDeleteIncome->bindValue(':idOfRow', $_SESSION['idIncomesDelete'], PDO::PARAM_INT);
        $queryDeleteIncome->bindValue(':idOfRow', $idIncomesDelete, PDO::PARAM_INT);
        $queryDeleteIncome->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryDeleteIncome->execute();

        return $queryDeleteIncome;
    }

    public function deleteExpense($idExpensesDelete)
    {
        $db = static::getDB();

        $sql = 'DELETE FROM expenses 
                WHERE id = :idOfRow
                AND user_id = :userId';

        $queryDeleteExpense = $db->prepare($sql);
        $queryDeleteExpense->bindValue(':idOfRow', $idExpensesDelete, PDO::PARAM_INT);
        $queryDeleteExpense->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
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

    public function insertIncomesIntoIncomesCategoryAssignedToUsers($currentUserId)
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

    public function getpaymentCategoryIncomeId($paymentCategoryIncomeName){

        // $paymentCategoryIncomeName = $_POST['paymentCategoryIncomeName'];
        $db = static::getDB();

        $sql = 'SELECT id FROM incomes_category_assigned_to_users WHERE name = :nameIncomeCategory AND user_id = :userId';

        $queryPaymentCategoryIncome = $db->prepare($sql);	
        $queryPaymentCategoryIncome->bindValue(':nameIncomeCategory', $paymentCategoryIncomeName, PDO::PARAM_STR);
        $queryPaymentCategoryIncome->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryPaymentCategoryIncome->execute();  
        
        $paymentCategoryIncomeId  = $queryPaymentCategoryIncome->fetch();

        return $paymentCategoryIncomeId['id'];
    }

    public function insertToIncomes()
    {
        if (!$this->validateIncomes()) {
            return $this->errors;
        }
        
        // $amountIncome = filter_input(INPUT_POST, 'amountIncome', FILTER_VALIDATE_FLOAT);
        // if ($amountIncome === false || $amountIncome <= 0 || $amountIncome > 999999.99) {
        //     $errors[] = 'Nieprawidłowa kwota';
        // }

        // $dateIncome = $_POST['dateIncome'] ?? '';
        // $d = DateTime::createFromFormat('Y-m-d', $dateIncome);
        // if (!$d || $d->format('Y-m-d') !== $dateIncome) {
        //     $errors[] = 'Nieprawidłowy format daty';
        // }

        // $commentIncome = mb_substr(trim($_POST['commentIncome'] ?? ''), 0, 100);

            // $paymentCatIncId = filter_var($this->data['paymentCategoryIncomeName'], FILTER_VALIDATE_INT);

            $paymentCategoryIncomeId = $this->getpaymentCategoryIncomeId($this->data['paymentCategoryIncomeName']);

            // $amountIncome = $_POST['amountIncome'];
		    // $dateIncome = $_POST['dateIncome'];

		    // $commentIncome = $_POST['commentIncome'];

            $db = static::getDB();

            $sql = 'INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment) VALUES (:userId, :paymentCategoryIncome, :amount, :dateIncome, :commentIncome)';

            $queryIncome = $db->prepare($sql);	
            $queryIncome->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
            $queryIncome->bindValue(':paymentCategoryIncome', $paymentCategoryIncomeId, PDO::PARAM_INT);
            $queryIncome->bindValue(':amount', $this->data['amountIncome'], PDO::PARAM_STR);
            $queryIncome->bindValue(':dateIncome', $this->data['dateIncome'], PDO::PARAM_STR);
            $queryIncome->bindValue(':commentIncome',  $this->data['commentIncome'], PDO::PARAM_STR);
    
            return $queryIncome->execute();
    }

    public function getPaymentId($paymentName)
    {
        $db = static::getDB();
        // $paymentName = $_POST['paymentMethod'];

        $sql = 'SELECT id FROM payment_methods_assigned_to_users WHERE name = :paymentName AND user_id = :userId';

        $paymentMethod = $db->prepare($sql);	
		$paymentMethod->bindValue(':paymentName', $paymentName, PDO::PARAM_STR);
		$paymentMethod->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
		$paymentMethod->execute();
	
		$getPaymentId = $paymentMethod->fetch();
        return $getPaymentId['id'];
    }

    public function getPaymentCategoryExpenseId($paymentCategoryExpense)
    {
        $db = static::getDB();
        // $paymentCategoryExpense = $_POST['paymentCategoryExpense'];

        $sql = 'SELECT id FROM expenses_category_assigned_to_users WHERE name = :nameExpCat AND user_id = :userId';

        $queryPaymentCategoryExpense = $db->prepare($sql);	
		$queryPaymentCategoryExpense->bindValue(':nameExpCat', $paymentCategoryExpense, PDO::PARAM_STR);
		$queryPaymentCategoryExpense->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
		$queryPaymentCategoryExpense->execute();

		$paymentCategoryExpenseId  = $queryPaymentCategoryExpense -> fetch();

        return $paymentCategoryExpenseId['id'];
    }

    // public function insertToExpenses($amountExpense, $dateExpense, $commentExpense, $paymentName, $paymentCategoryExpense)
    public function insertToExpenses()
    {
        if (!$this->validateExpenses()) {
            return $this->errors;
        }

        $paymentCatExpenseId = $this->getpaymentCategoryExpenseId($this->data['paymentCategoryExpense']);

        $paymentId = $this->getPaymentId($this->data['paymentMethod']);

        $db = static::getDB();

        // $amountExpense = $_POST['amountExpense'];
        // $dateExpense = $_POST['dateExpense'];
        // $commentExpense = $_POST['commentExpense'];

        $sql = 'INSERT INTO expenses (user_id, expense_category_assigned_to_user_id, payment_method_assigned_to_user_id, amount, date_of_expense, expense_comment) VALUES (:userId, :expense_category, :payment_method, :amount, :dateExpense, :commentExpense)';

        $queryExpense = $db->prepare($sql);	
		$queryExpense->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
		$queryExpense->bindValue(':expense_category', $paymentCatExpenseId, PDO::PARAM_INT);
		$queryExpense->bindValue(':payment_method', $paymentId, PDO::PARAM_INT);
		$queryExpense->bindValue(':amount', $this->data['amountExpense'], PDO::PARAM_STR);
		$queryExpense->bindValue(':dateExpense', $this->data['dateExpense'], PDO::PARAM_STR);
		$queryExpense->bindValue(':commentExpense', $this->data['commentExpense'], PDO::PARAM_STR);
		
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

    public static function selectNameFromIncomesCategoryToEdit($editIncCatID)
    {
        $db = static::getDB();
        $sql = 'SELECT 
                name, id 
                FROM incomes_category_assigned_to_users
                WHERE id = :id
                AND user_id = :userId';

        $queryEditIncome = $db->prepare($sql);
        $queryEditIncome->bindValue(':id', $editIncCatID, PDO::PARAM_INT);
        $queryEditIncome->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryEditIncome->execute();

        $nameOfIncomeCategory  = $queryEditIncome -> fetch(); 

        return [
            'id' => $nameOfIncomeCategory['id'],
            'name' => $nameOfIncomeCategory['name']
        ];
    }

    public static function selectNameFromExpensesCategoryToEdit($editExpCatID)
    {
        $db = static::getDB();
        $sql = 'SELECT 
                name, id
                FROM expenses_category_assigned_to_users
                WHERE id = :id
                AND user_id = :userId';

        $queryEditExpense = $db->prepare($sql);
        $queryEditExpense->bindValue(':id', $editExpCatID, PDO::PARAM_INT);
        $queryEditExpense->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryEditExpense->execute();
        $nameOfExpenseCategory  = $queryEditExpense -> fetch(); 

        return [
            'id' => $nameOfExpenseCategory['id'],
            'name' => $nameOfExpenseCategory['name']
        ];      
    }

    public static function selectNameFromPayMethCategoryToEdit($editPayMethCatID)
    {
        $db = static::getDB();
        $sql = 'SELECT 
                name, id
                FROM payment_methods_assigned_to_users
                WHERE id = :id
                AND user_id = :userId';

        $queryEditPayMeth = $db->prepare($sql);
        $queryEditPayMeth->bindValue(':id', $editPayMethCatID, PDO::PARAM_INT);
        $queryEditPayMeth->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryEditPayMeth->execute();

        $nameOfPayMethCategory  = $queryEditPayMeth -> fetch(); 

        return [
            'id' => $nameOfPayMethCategory['id'],
            'name' => $nameOfPayMethCategory['name']
        ];       
    }

    public static function selectNameFromIncomesCategoryToDelete($deleteIncCatID)
    {
        $db = static::getDB();
        $sql = 'SELECT 
                name, id
                FROM incomes_category_assigned_to_users
                WHERE id = :id
                AND user_id = :userId';

        $queryDeleteIncome = $db->prepare($sql);
        $queryDeleteIncome->bindValue(':id', $deleteIncCatID, PDO::PARAM_INT);
        $queryDeleteIncome->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryDeleteIncome->execute();

        $nameOfIncomeCategory  = $queryDeleteIncome -> fetch(); 

        // return $nameOfIncomeCategory['name'];   
        return [
            'id' => $nameOfIncomeCategory['id'],
            'name' => $nameOfIncomeCategory['name']
        ];     
    }

    public static function selectNameFromExpensesCategoryToDelete($deleteExpCatID)
    {
        $db = static::getDB();
        $sql = 'SELECT 
                name, id
                FROM expenses_category_assigned_to_users
                WHERE id = :id
                AND user_id = :userId';

        $queryDeleteExpense = $db->prepare($sql);
        $queryDeleteExpense->bindValue(':id', $deleteExpCatID, PDO::PARAM_INT);
        $queryDeleteExpense->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryDeleteExpense->execute();

        $nameOfExpenseCategory  = $queryDeleteExpense -> fetch(); 

        // return $nameOfExpenseCategory['name'];
        return [
            'id' => $nameOfExpenseCategory['id'],
            'name' => $nameOfExpenseCategory['name']
        ];         
    }

    public static function selectNameFromExpensesCategoryToLimit($limitExpID)
    {
        $db = static::getDB();
        $sql = 'SELECT 
                name, id
                FROM expenses_category_assigned_to_users
                WHERE id = :id
                AND user_id = :userId';

        $queryLimitName = $db->prepare($sql);
        $queryLimitName->bindValue(':id', $limitExpID, PDO::PARAM_INT);
        $queryLimitName->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryLimitName->execute();

        $nameOfExpenseCategory  = $queryLimitName -> fetch(); 

        // return $nameOfExpenseCategory['name']; 
        return [
            'id' => $nameOfExpenseCategory['id'],
            'name' => $nameOfExpenseCategory['name']
        ];        
    }
    

    public static function selectNameFromPayMethCategoryToDelete($deleteID)
    {
        $db = static::getDB();
        $sql = 'SELECT 
                name, id 
                FROM payment_methods_assigned_to_users
                WHERE id = :id
                AND user_id = :userId';

        $queryDeletePayMeth = $db->prepare($sql);
        $queryDeletePayMeth->bindValue(':id', $deleteID, PDO::PARAM_INT);
        $queryDeletePayMeth->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryDeletePayMeth->execute();

        $nameOfPayMethCategory  = $queryDeletePayMeth -> fetch(); 

        // return $nameOfPayMethCategory['name']; 
        return [
            'id' => $nameOfPayMethCategory['id'],
            'name' => $nameOfPayMethCategory['name']
        ];        
    }

    public function validateCategoryOfIncomes()
    {
        $this->data['editIncomeCategoryName'] = mb_substr(trim($_POST['editIncomeCategoryName'] ?? ''), 0, 50);

        // $this->data['editIncomeCategoryName'] = mb_substr(trim($_POST['editIncomeCategoryName'] ?? ''), 0, 50);

        if ($this->data['editIncomeCategoryName'] === '') {
            $this->errors[] = 'Nazwa kategorii jest wymagana';
        }

        $incomeCategoryEditedID = filter_var($this->data['incomeCategoryEditedID'], FILTER_VALIDATE_INT);
        if (!$incomeCategoryEditedID) {
            $this->errors[] = 'Błędne ID kategorii przychodu';
            return $this->errors;
        }

        return empty($this->errors);
    }

    // public function editIncomesCategory($editIncomeCategoryName, $editIncomeCategoryID)
    public function editIncomesCategory()
    {
        if (!$this->validateCategoryOfIncomes()) {
            return $this->errors;
        }

        $db = static::getDB();

        $sql = 'UPDATE incomes_category_assigned_to_users 
                SET name  = :income_category
                WHERE id=:incomeCategoryEditId
                AND user_id = :userId';

        $queryEditIncome = $db->prepare($sql);
		$queryEditIncome->bindValue(':income_category', $this->data['editIncomeCategoryName'], PDO::PARAM_STR);
        $queryEditIncome->bindValue(':incomeCategoryEditId', $this->data['incomeCategoryEditedID'], PDO::PARAM_INT);
        $queryEditIncome->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        
        return $queryEditIncome->execute();
    }

    public function setLimitValueDB($editIncomeCategoryName, $limitID)
    {
        $db = static::getDB();

        $sql = 'UPDATE expenses_category_assigned_to_users 
                SET limit_value  = :limit_value
                WHERE id=:limitId
                AND user_id = :userId';

        $queryEditIncome = $db->prepare($sql);
		$queryEditIncome->bindValue(':limit_value', $editIncomeCategoryName, PDO::PARAM_INT);
        $queryEditIncome->bindValue(':limitId', $limitID, PDO::PARAM_INT);
        $queryEditIncome->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        
        return $queryEditIncome->execute();
    }

    public static function selectLimitValueUserIdCategoryName($categoryExpenseName)
    {
        $db = static::getDB();

        $sql = 'SELECT 
                limit_value
                FROM expenses_category_assigned_to_users
                WHERE user_id = :userId AND name LIKE :categoryName';

        $queryLimitValue = $db->prepare($sql);
        $queryLimitValue->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryLimitValue->bindValue(':categoryName', $categoryExpenseName, PDO::PARAM_STR);
        $queryLimitValue->execute();

        $valueOfLimit  = $queryLimitValue -> fetch(); 

        return $valueOfLimit['limit_value'];
    }

    public static function selectSumOfExpensesForParticularCategoryAndDate($categoryExpenseName, $year, $month)
    {
        $db = static::getDB();

        $sql = 'SELECT SUM(e.amount) AS total
                FROM expenses e
                JOIN expenses_category_assigned_to_users c
                    ON e.expense_category_assigned_to_user_id = c.id
                WHERE c.user_id = :userId 
                AND c.name = :categoryName
                AND YEAR(e.date_of_expense) = :year
                AND MONTH(e.date_of_expense) = :month';

        $querySumOfExpensesJavaScript = $db->prepare($sql);
        $querySumOfExpensesJavaScript->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $querySumOfExpensesJavaScript->bindValue(':categoryName', $categoryExpenseName, PDO::PARAM_STR);
        $querySumOfExpensesJavaScript->bindValue(':year', $year, PDO::PARAM_INT);
        $querySumOfExpensesJavaScript->bindValue(':month', $month, PDO::PARAM_INT);
        $querySumOfExpensesJavaScript->execute();

        $sumOfExpenses  = $querySumOfExpensesJavaScript -> fetch(); 

        return $sumOfExpenses['total'];
    }

    public static function selectValueOfLimit($limitExpID)
    {
        $db = static::getDB();

        $sql = 'SELECT 
                limit_value
                FROM expenses_category_assigned_to_users
                WHERE id = :id
                AND user_id = :userId';

        $queryLimitValue = $db->prepare($sql);
        $queryLimitValue->bindValue(':id', $limitExpID, PDO::PARAM_INT);
        $queryLimitValue->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryLimitValue->execute();

        $valueOfLimit  = $queryLimitValue -> fetch(); 

        return $valueOfLimit['limit_value'];
        
    }

    public function validateCategoryOfExpenses(){
        $this->data['editExpenseCategoryName'] = mb_substr(trim($_POST['editExpenseCategoryName'] ?? ''), 0, 50);
        if ($this->data['editExpenseCategoryName'] === '') {
            // Flash::addMessage('Nazwa kategorii jest wymagana', Flash::WARNING);
            // $this->redirect('/profile/categoryconfigurator');
            $this->errors[] = 'Nazwa kategorii jest wymagana';
        }
        // $editExpenseCategoryName = $_POST['editExpenseCategoryName'];
        // if (!$editExpenseCategoryName) {
        //     ($this->redirect('/profile/categoryconfigurator'));
        // }
        $editExpenseCategoryID = filter_input(INPUT_POST, 'expenseCategoryEditedID', FILTER_VALIDATE_INT);
        if (!$editExpenseCategoryID) {
            $this->errors[] = 'Błędne ID kategorii przychodu';
            return $this->errors;
        }
        return empty($this->errors);
    }

    public function editExpensesCategory()
    {
        if (!$this->validateCategoryOfExpenses()) {
            return $this->errors;
        }

        $db = static::getDB();

        $sql = 'UPDATE expenses_category_assigned_to_users 
                SET name  = :expense_category
                WHERE id=:expenseCategoryEditId
                AND user_id = :userId';

        $queryEditExpense = $db->prepare($sql);
		$queryEditExpense->bindValue(':expense_category', $this->data['editExpenseCategoryName'], PDO::PARAM_STR);
        $queryEditExpense->bindValue(':expenseCategoryEditId', $this->data['expenseCategoryEditedID'], PDO::PARAM_INT);
        $queryEditExpense->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        
        return $queryEditExpense->execute();
    }

    public function editPayMethCategory($editPayMethCategoryName, $payMethCatID)
    {
        $db = static::getDB();

        $sql = 'UPDATE payment_methods_assigned_to_users 
                SET name  = :pay_meth_category
                WHERE id=:payMethCategoryEditId
                AND user_id = :userId';

        $queryEditPayment = $db->prepare($sql);
		$queryEditPayment->bindValue(':pay_meth_category', $editPayMethCategoryName, PDO::PARAM_STR);
        $queryEditPayment->bindValue(':payMethCategoryEditId', $payMethCatID, PDO::PARAM_INT);
        $queryEditPayment->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        
        return $queryEditPayment->execute();
    }

    public function deleteIncomesCategory($deleteId)
    {
        $db = static::getDB();

        $sql = 'DELETE FROM incomes_category_assigned_to_users 
                WHERE id = :idOfIncomeCategory
                AND user_id = :userId';

        $queryDeleteIncomeCategory = $db->prepare($sql);
        $queryDeleteIncomeCategory->bindValue(':idOfIncomeCategory', $deleteId, PDO::PARAM_INT);
        $queryDeleteIncomeCategory->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryDeleteIncomeCategory->execute();

        return $queryDeleteIncomeCategory;
    } 

    public function deleteExpensesCategory($deleteExpensesID)
    {
        $db = static::getDB();

        $sql = 'DELETE FROM expenses_category_assigned_to_users 
                WHERE id = :idOfExpenseCategory
                AND user_id = :userId';

        $queryDeleteIncomeCategory = $db->prepare($sql);
        $queryDeleteIncomeCategory->bindValue(':idOfExpenseCategory', $deleteExpensesID, PDO::PARAM_INT);
        $queryDeleteIncomeCategory->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryDeleteIncomeCategory->execute();

        return $queryDeleteIncomeCategory;
    } 

    public function deletePayMethCategory($deletePaymentID)
    {
        $db = static::getDB();

        $sql = 'DELETE FROM payment_methods_assigned_to_users 
                WHERE id = :idOfPayMethCategory
                AND user_id = :userId';

        $queryDeleteIncomeCategory = $db->prepare($sql);
        $queryDeleteIncomeCategory->bindValue(':idOfPayMethCategory', $deletePaymentID, PDO::PARAM_INT);
        $queryDeleteIncomeCategory->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryDeleteIncomeCategory->execute();

        return $queryDeleteIncomeCategory;
    }

    public function deleteIncomesRowRelatedToIncomesCatAssignedToUserId($deleteId)
    {
        $db = static::getDB();

        $sql = 'DELETE FROM incomes 
                WHERE income_category_assigned_to_user_id = :idOfIncomeCategory
                AND user_id = :userId';

        $queryDeleteIncomesRowRelatedToIncCatAssignedToUser = $db->prepare($sql);
        $queryDeleteIncomesRowRelatedToIncCatAssignedToUser->bindValue(':idOfIncomeCategory', $deleteId, PDO::PARAM_INT);
        $queryDeleteIncomesRowRelatedToIncCatAssignedToUser->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryDeleteIncomesRowRelatedToIncCatAssignedToUser->execute();

        return $queryDeleteIncomesRowRelatedToIncCatAssignedToUser;
    } 

    public function deleteExpensesRowRelatedToExpensesCatAssignedToUserId($deleteExpensesID)
    {
        $db = static::getDB();

        $sql = 'DELETE FROM expenses 
                WHERE expense_category_assigned_to_user_id = :idOfExpenseCategory
                AND user_id = :userId';

        $queryDeleteExpensesRowRelatedToExpCatAssignedToUser = $db->prepare($sql);
        $queryDeleteExpensesRowRelatedToExpCatAssignedToUser->bindValue(':idOfExpenseCategory', $deleteExpensesID, PDO::PARAM_INT);
        $queryDeleteExpensesRowRelatedToExpCatAssignedToUser->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
        $queryDeleteExpensesRowRelatedToExpCatAssignedToUser->execute();

        return $queryDeleteExpensesRowRelatedToExpCatAssignedToUser;
    }

    public function deleteExpensesRowRelatedToPayMethCatAssignedToUserId($deletePaymentID)
    {
        $db = static::getDB();

        $sql = 'DELETE FROM expenses 
                WHERE payment_method_assigned_to_user_id = :idOfPayMethCategory
                AND user_id = :userId';

        $queryDeleteExpensesRowRelatedToPayMethCatAssignedToUser = $db->prepare($sql);
        $queryDeleteExpensesRowRelatedToPayMethCatAssignedToUser->bindValue(':idOfPayMethCategory', $deletePaymentID, PDO::PARAM_INT);
        $queryDeleteExpensesRowRelatedToPayMethCatAssignedToUser->bindValue(':userId', $_SESSION['userIdSession'], PDO::PARAM_INT);
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

    // public function deleteFromDataBaseUser($id)
    // {
    //     $db = static::getDB();

    //     $sql = 'DELETE FROM users 
    //             WHERE id = :idOfUser';

    //     $queryDeleteUser = $db->prepare($sql);
    //     $queryDeleteUser->bindValue(':idOfUser', $id, PDO::PARAM_INT);
    //     $queryDeleteUser->execute();

    //     return $queryDeleteUser;
    // }

    // public function deleteFromDataBaseIncomesUserID($id)
    // {
    //     $db = static::getDB();

    //     $sql = 'DELETE FROM incomes 
    //             WHERE user_id = :idOfUser';

    //     $queryDeleteIncomesUser = $db->prepare($sql);
    //     $queryDeleteIncomesUser->bindValue(':idOfUser', $id, PDO::PARAM_INT);
    //     $queryDeleteIncomesUser->execute();

    //     return $queryDeleteIncomesUser;
    // }

    // public function deleteFromDataBaseExpensesUserID($id)
    // {
    //     $db = static::getDB();

    //     $sql = 'DELETE FROM expenses 
    //             WHERE user_id = :idOfUser';

    //     $queryDeleteExpensesUser = $db->prepare($sql);
    //     $queryDeleteExpensesUser->bindValue(':idOfUser', $id, PDO::PARAM_INT);
    //     $queryDeleteExpensesUser->execute();

    //     return $queryDeleteExpensesUser;
    // }

    // public function deleteFromDataBaseIncomesCategoryAssignedToUser($id)
    // {
    //     $db = static::getDB();

    //     $sql = 'DELETE FROM incomes_category_assigned_to_users 
    //             WHERE user_id = :idOfUser';

    //     $queryDeleteIncAssignedToUser = $db->prepare($sql);
    //     $queryDeleteIncAssignedToUser->bindValue(':idOfUser', $id, PDO::PARAM_INT);
    //     $queryDeleteIncAssignedToUser->execute();

    //     return $queryDeleteIncAssignedToUser;
    // }

    // public function deleteFromDataBaseExpensesCategoryAssignedToUser($id)
    // {
    //     $db = static::getDB();

    //     $sql = 'DELETE FROM expenses_category_assigned_to_users 
    //             WHERE user_id = :idOfUser';

    //     $queryDeleteExpAssignedToUser = $db->prepare($sql);
    //     $queryDeleteExpAssignedToUser->bindValue(':idOfUser', $id, PDO::PARAM_INT);
    //     $queryDeleteExpAssignedToUser->execute();

    //     return $queryDeleteExpAssignedToUser;
    // }
    
    // public function deleteFromDataBasePaymentMethodsCategoryAssignedToUser($id)
    // {
    //     $db = static::getDB();

    //     $sql = 'DELETE FROM payment_methods_assigned_to_users WHERE user_id = :idOfUser';

    //     $queryDeletePayMethAssignedToUser = $db->prepare($sql);
    //     $queryDeletePayMethAssignedToUser->bindValue(':idOfUser', $id, PDO::PARAM_INT);
    //     $queryDeletePayMethAssignedToUser->execute();

    //     return $queryDeletePayMethAssignedToUser;
    // }

    public function deleteAccountFromDataBase($userID): bool
    {
        $db = static::getDB();

        try {
            $db->beginTransaction();
            
            $tables = [
                'incomes',
                'expenses', 
                'incomes_category_assigned_to_users',
                'expenses_category_assigned_to_users',
                'payment_methods_assigned_to_users',
            ];
            
            foreach ($tables as $table) {
                $stmt = $db->prepare("DELETE FROM {$table} WHERE user_id = :id");
                $stmt->execute([':id' => $userID]);
            }
            
            $stmt = $db->prepare('DELETE FROM users WHERE id = :id');
            $stmt->execute([':id' => $userID]);
            
            $db->commit();
            return true;
            
        } catch (\PDOException $e) {
            $db->rollBack();
            error_log($e->getMessage());
            return false;
        }
    }
}