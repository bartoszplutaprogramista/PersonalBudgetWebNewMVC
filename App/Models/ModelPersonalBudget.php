<?php


namespace App\Models;




use PDO;
use \App\Token;
use \App\Mail;
use \Core\View;
use \App\Models\User;


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

    public function getUserId($emailOfUser)
    {

        $db = static::getDB();
        $queryId = $db->prepare('SELECT id FROM users WHERE email = :email');	
        $queryId->bindValue(':email', $emailOfUser, PDO::PARAM_STR);
        $queryId->execute();
    
        $userId = $queryId->fetch();

        return $userId['id'];
    }

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

    public function insertToIncomes($user)
    {

   //     $this->validate();

        if (empty($this->errors)){

            $array = get_object_vars($user);

            // echo "Wartość email moja w insert to incomes : ".$array['email'];


            $personalBudget = new ModelPersonalBudget($_POST);
            // // // $email = $_POST['email'];
            // $personalBudget->validateNameAndEmail(); 
      //      $userId = $personalBudget->getUserId();

            // $user = new User($_POST);
            $userId = $personalBudget->getUserId($array['email']);

            $idFromIncomes = $personalBudget->getIdFromIncomesCategoryAssignedToUsers($userId);

            // $userId = static::getUserId();
            // $userId = $user->getUserId();
            $amountIncome = $_POST['amountIncome'];
		    $dateIncome = $_POST['dateIncome'];
		//    $paymentCategoryIncomeName = $_POST['paymentCategoryIncomeName'];
		    $commentIncome = $_POST['commentIncome'];

            $db = static::getDB();

            $queryIncome = $db->prepare('INSERT INTO incomes (user_id, income_category_assigned_to_user_id, amount, date_of_income, income_comment) VALUES (:userId, :paymentCategoryIncome, :amount, :dateIncome, :commentIncome)');	
            $queryIncome->bindValue(':userId', $userId, PDO::PARAM_INT);
            $queryIncome->bindValue(':paymentCategoryIncome', $idFromIncomes, PDO::PARAM_INT);
            $queryIncome->bindValue(':amount', $amountIncome, PDO::PARAM_STR);
            $queryIncome->bindValue(':dateIncome', $dateIncome, PDO::PARAM_STR);
            $queryIncome->bindValue(':commentIncome', $commentIncome, PDO::PARAM_STR);
    
            return $queryIncome->execute();
        }
        return false;
    }

}
