<?php


namespace App\Models;




use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;
// use \App\Models\User;


// use \Models\User;

/**
 * User model
 *
 * PHP version 7.0
 */

 #[\AllowDynamicProperties]
// class ModelPersonalBudget extends \App\Controllers\Authenticated
class ModelPersonalBudget extends \Core\Model
{

    public $amountIncome;
    public $dateIncome;
    public $commentIncome;
    public $userId;
    public $email;
    public $paymentCategoryIncomeName;
    // public $user;

    // public function __construct($data = [])
    // {
    //     foreach ($data as $key => $value) {
    //         $this->$key = $value;
    //     };
    // } 

    // protected function before()
    // {
    //     // parent::before();

    //     $this->user = Auth::getUser();
    // }
    // protected function validateNameAndEmail() 
    // {
    //     // Name
    //     // if ($this->name == '') {
    //     //     // $this->errors['name'] = 'Wprowadź imię.';
    //     //     echo 'Wprowadź imię.';
    //     // }
 
    //     // email address
    //     if (filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
    //         // $this->errors['email'] = 'Podaj poprawny adres e-mail.';
    //         echo 'Podaj poprawny adres e-mail.';
    //         echo 'Wartość email.'.$this->email;
    //     }else {
    //         echo $this->email;
    //     }
    //     if (User::emailExists($this->email, $this->id ?? null)) {
    //         // $this->errors['email'] = 'Istnieje konto o podanym adresie e-mail.';
    //         echo 'Istnieje konto o podanym adresie e-mail.';
    //     }       
    // }
    // protected function before()
    // {
    //     parent::before();

    //     $this->user = Auth::getUser();
    // }

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

    // public static function getQueryNamePaymentMethodsDefault()
    // {
    //     $queryNamePaymentMethodsDefault = $db->prepare('SELECT name FROM payment_methods_default');	
    //     $queryNamePaymentMethodsDefault->execute();

    //     $queryNamePayment = $queryNamePaymentMethodsDefault->fetchAll();

    //     return $queryNamePayment;
    // }

    public static function getQueryNameIncome($userId)
    {
        // $array = get_object_vars($user);
        // $user_object = new User($_POST);
        // $userId = $user_object->getUserId($array['email']);

        $timeYear = date('Y', strtotime("-1 MONTH"));
        $timeMonth = date('m', strtotime("-1 MONTH"));
        $fullDateLastMonth = $timeYear."-".$timeMonth."%";

        $dateCurrentYear = date("Y");
        $fullDateCurrentYear = $dateCurrentYear."%";

        // echo "data: " . $fullDateLastMonth;

        $db = static::getDB();
        $queryNameIncome = $db->prepare('SELECT * FROM incomes_category_assigned_to_users INNER JOIN incomes ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id WHERE incomes.user_id = :userId AND date_of_income LIKE :dataHelpLastMonth ORDER BY date_of_income ASC');
		$queryNameIncome->bindValue(':userId', $userId, PDO::PARAM_INT);
		$queryNameIncome->bindValue(':dataHelpLastMonth', $fullDateCurrentYear, PDO::PARAM_STR);
		$queryNameIncome->execute();

		$queryName = $queryNameIncome->fetchAll(); 

        // print_r ($queryName);
        return $queryName;      
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

        ///////////////////
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

    // public function getUserId($emailOfUser)
    // {

    //     $db = static::getDB();
    //     $queryId = $db->prepare('SELECT id FROM users WHERE email = :email');	
    //     $queryId->bindValue(':email', $emailOfUser, PDO::PARAM_STR);
    //     $queryId->execute();
    
    //     $userId = $queryId->fetch();

    //     return $userId['id'];
    // }

    public function getIdFromIncomesCategoryAssignedToUsers($id)
    {

        $db = static::getDB();
        $queryPaymentCategoryIncome = $db->prepare('SELECT id FROM incomes_category_assigned_to_users WHERE name = :nameIncomeCategory AND user_id = :userId');	
		$queryPaymentCategoryIncome->bindValue(':nameIncomeCategory', $paymentCategoryIncomeName, PDO::PARAM_STR);
		$queryPaymentCategoryIncome->bindValue(':userId', $id, PDO::PARAM_INT);
		$queryPaymentCategoryIncome->execute();

		$paymentCategoryIncomeId  = $queryPaymentCategoryIncome->fetch();

        // parent::before();

        // $this->user = Auth::getUser();  

        // echo "Wartość email: ";
        // print_r ($this->user);

        // $array = get_object_vars($this->user);

        // echo "Wartość email moja : ".$array['email'];
        // print_r($this->user, email);

        // echo "wartość emaila: ".$this->email;
        // exit;
        // echo "user.email= ".$this->email;

        // echo '$userId wynosi": '.$userId['id'];
        // exit;
        // $nowa = $userId;

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

   //     $this->validate();

        if (empty($this->errors)){

            $array = get_object_vars($user);

            // echo "Wartość email moja w insert to incomes : ".$array['email'];



            $personalBudget = new ModelPersonalBudget($_POST);

            $user = new User($_POST);
            // // // $email = $_POST['email'];
            // $personalBudget->validateNameAndEmail(); 
      //      $userId = $personalBudget->getUserId();

            // $user = new User($_POST);
            $userId = $user->getUserId($array['email']);

            $paymentCategoryIncomeId = $personalBudget->getpaymentCategoryIncomeId($userId);

            // $idFromIncomes = $personalBudget->getIdFromIncomesCategoryAssignedToUsers($userId);

            // $userId = static::getUserId();
            // $userId = $user->getUserId();
            $amountIncome = $_POST['amountIncome'];
		    $dateIncome = $_POST['dateIncome'];
		//    $paymentCategoryIncomeName = $_POST['paymentCategoryIncomeName'];
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
    
    public function displayCurrentMonth(){
        $dateFromTo = $dataHelpYearMonth."-01 do ".$currentDate; 

		$queryNameIncome = $db->prepare('SELECT * FROM incomes_category_assigned_to_users INNER JOIN incomes ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id WHERE incomes.user_id = :userId AND date_of_income LIKE :dataHelpCurrentMonth ORDER BY date_of_income ASC');
		$queryNameIncome->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$queryNameIncome->bindValue(':dataHelpCurrentMonth', $dataHelpCurrentMonth, PDO::PARAM_STR);
		$queryNameIncome->execute();

		$queryName = $queryNameIncome->fetchAll();

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
		$queryNameExpense->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$queryNameExpense->bindValue(':dataHelpCurrentMonth', $dataHelpCurrentMonth, PDO::PARAM_STR);
		$queryNameExpense->execute();

		$queryExpense = $queryNameExpense->fetchAll();

		$querySumIncomes = $db->prepare('SELECT SUM(amount) AS incSum FROM incomes WHERE user_id = :userId AND date_of_income LIKE :dataHelpCurrentMonth');
		$querySumIncomes->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$querySumIncomes->bindValue(':dataHelpCurrentMonth', $dataHelpCurrentMonth, PDO::PARAM_STR);
		$querySumIncomes->execute();

		$incomesSum = $querySumIncomes->fetch();

		$querySumExpenses = $db->prepare('SELECT SUM(amount) AS expSum FROM expenses WHERE user_id = :userId AND date_of_expense LIKE :dataHelpCurrentMonth');
		$querySumExpenses->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$querySumExpenses->bindValue(':dataHelpCurrentMonth', $dataHelpCurrentMonth, PDO::PARAM_STR);
		$querySumExpenses->execute();

		$expensesSum = $querySumExpenses->fetch();
    }

    public function displayLastMonth(){
        $timeHowManyDays = date('t', strtotime("-1 MONTH"));
		$timeMonth = date('m', strtotime("-1 MONTH"));
		$timeYear = date('Y', strtotime("-1 MONTH"));

		$dateFromTo = $timeYear."-".$timeMonth."-01 do ".$timeYear."-".$timeMonth."-".$timeHowManyDays;
		
		$fullDateLastMonth = $timeYear."-".$timeMonth."%";

		$queryNameIncome = $db->prepare('SELECT * FROM incomes_category_assigned_to_users INNER JOIN incomes ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id WHERE incomes.user_id = :userId AND date_of_income LIKE :dataHelpLastMonth ORDER BY date_of_income ASC');
		$queryNameIncome->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$queryNameIncome->bindValue(':dataHelpLastMonth', $fullDateLastMonth, PDO::PARAM_STR);
		$queryNameIncome->execute();

		$queryName = $queryNameIncome->fetchAll();
		
		$queryNameExpense = $db->prepare('SELECT 
		ex.amount AS amn,
		ex.date_of_expense AS dateExp,
		pay.name AS pay,
		exCat.name AS excategory,
		ex.expense_comment AS comment
		FROM expenses_category_assigned_to_users AS exCat 
		INNER JOIN expenses AS ex ON exCat.id = ex.expense_category_assigned_to_user_id 
		INNER JOIN payment_methods_assigned_to_users AS pay ON ex.payment_method_assigned_to_user_id = pay.id
		WHERE ex.user_id = :userId AND date_of_expense LIKE :dataHelpLastMonth 
		ORDER BY date_of_expense ASC');
		$queryNameExpense->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$queryNameExpense->bindValue(':dataHelpLastMonth', $fullDateLastMonth, PDO::PARAM_STR);
		$queryNameExpense->execute();

		$queryExpense = $queryNameExpense->fetchAll();

		$querySumIncomes = $db->prepare('SELECT SUM(amount) AS incSum FROM incomes WHERE user_id = :userId AND date_of_income LIKE :dataHelpLastMonth');
		$querySumIncomes->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$querySumIncomes->bindValue(':dataHelpLastMonth', $fullDateLastMonth, PDO::PARAM_STR);
		$querySumIncomes->execute();

		$incomesSum = $querySumIncomes->fetch();

		$querySumExpenses = $db->prepare('SELECT SUM(amount) AS expSum FROM expenses WHERE user_id = :userId AND date_of_expense LIKE :dataHelpLastMonth');
		$querySumExpenses->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$querySumExpenses->bindValue(':dataHelpLastMonth', $fullDateLastMonth, PDO::PARAM_STR);
		$querySumExpenses->execute();

		$expensesSum = $querySumExpenses->fetch();
    }

    public function displayCurrentYear(){
        $dateFromTo = 	$dateCurrentYear."-01-01 do ".$currentDate;
		$fullDateCurrentYear = $dateCurrentYear."%";

		$queryNameIncome = $db->prepare('SELECT * FROM incomes_category_assigned_to_users INNER JOIN incomes ON incomes_category_assigned_to_users.id = incomes.income_category_assigned_to_user_id WHERE incomes.user_id = :userId AND date_of_income LIKE :dataHelpCurrentYear ORDER BY date_of_income ASC');
		$queryNameIncome->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$queryNameIncome->bindValue(':dataHelpCurrentYear', $fullDateCurrentYear, PDO::PARAM_STR);
		$queryNameIncome->execute();

		$queryName = $queryNameIncome->fetchAll();

		$queryNameExpense = $db->prepare('SELECT 
		ex.amount AS amn,
		ex.date_of_expense AS dateExp,
		pay.name AS pay,
		exCat.name AS excategory,
		ex.expense_comment AS comment
		FROM expenses_category_assigned_to_users AS exCat 
		INNER JOIN expenses AS ex ON exCat.id = ex.expense_category_assigned_to_user_id 
		INNER JOIN payment_methods_assigned_to_users AS pay ON ex.payment_method_assigned_to_user_id = pay.id
		WHERE ex.user_id = :userId AND date_of_expense LIKE :dataHelpCurrentYear 
		ORDER BY date_of_expense ASC');
		$queryNameExpense->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$queryNameExpense->bindValue(':dataHelpCurrentYear', $fullDateCurrentYear, PDO::PARAM_STR);
		$queryNameExpense->execute();

		$queryExpense = $queryNameExpense->fetchAll();

		$querySumIncomes = $db->prepare('SELECT SUM(amount) AS incSum FROM incomes WHERE user_id = :userId AND date_of_income LIKE :dataHelpCurrentYear');
		$querySumIncomes->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$querySumIncomes->bindValue(':dataHelpCurrentYear', $fullDateCurrentYear, PDO::PARAM_STR);
		$querySumIncomes->execute();

		$incomesSum = $querySumIncomes->fetch();

		$querySumExpenses = $db->prepare('SELECT SUM(amount) AS expSum FROM expenses WHERE user_id = :userId AND date_of_expense LIKE :dataHelpCurrentYear');
		$querySumExpenses->bindValue(':userId', $_SESSION['userId'], PDO::PARAM_INT);
		$querySumExpenses->bindValue(':dataHelpCurrentYear', $fullDateCurrentYear, PDO::PARAM_STR);
		$querySumExpenses->execute();

		$expensesSum = $querySumExpenses->fetch();
    }

}
