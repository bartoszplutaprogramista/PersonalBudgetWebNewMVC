<?php
//Personal Budget
namespace App\Controllers;

use \Core\View;
use \App\Auth;
use \App\Flash;
use \App\Models\ModelPersonalBudget;
use \App\Models\User;
use \App\Csrf;
use DateTime;
use GeminiAPI\Client;
use GeminiAPI\Resources\ModelName;
use GeminiAPI\Resources\Parts\TextPart;


#[\AllowDynamicProperties]
class Personalbudget extends Authenticated
{
    public $user;

    protected function before()
    {
        parent::before();

        $this->user = Auth::getUser();
    }

    public function addIncomeAction()
    {
         $incomes_options_form = \App\Models\ModelPersonalBudget::selectOptionsForIncomes();

        $formData = Flash::getFormData('new_income_form');

        View::renderTemplate('PersonalBudget/addIncome.html', [
            'user' => $this->user,
            'incomes_options_form' => $incomes_options_form,
            'formData' => $formData,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function addExpenseAction()
    {
        $expenses_options_form_category = \App\Models\ModelPersonalBudget::selectOptionsForExpensesCategory();           
        $expenses_options_form_payment_method = \App\Models\ModelPersonalBudget::selectOptionsForExpensesPaymentMethod(); 
        $formDataExpenses = Flash::getFormData('new_expense_form');

        View::renderTemplate('PersonalBudget/addExpense.html', [
            'user' => $this->user,
            'expenses_options_form_category' => $expenses_options_form_category,
            'expenses_options_form_payment_method' => $expenses_options_form_payment_method,
            'formDataExpenses' => $formDataExpenses,
            'csrf_token' => Csrf::generate()
        ]);
    } 

    public function browseTheBalanceAction()
    {
        View::renderTemplate('PersonalBudget/browseTheBalance.html', [
            'user' => $this->user,
            'csrf_token' => Csrf::generate()
        ]);
    } 

    public function successDeletedExpenseAction()
    {
        View::renderTemplate('PersonalBudget/successDeletedExpense.html', [
            'user' => $this->user,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function successAreyouSuredeleteFromIncomesAction()
    {
        $id_incomes_delete = (int)$this->route_params['idincomesdelete'];
        $data_to_are_you_sure_table_incomes = \App\Models\ModelPersonalBudget::selectAllFromIncomesToEdit($id_incomes_delete);
        if (!$data_to_are_you_sure_table_incomes) {
            Flash::addMessage('Nie znaleziono id lub nie należy do Twojego konta', Flash::WARNING);
            $this->redirect('/personalbudget/browsethebalance');
        }
        $ordinal_delete_incomes_number = (int)$this->route_params['myordinalnumberdeleteincomesvar'];
        $which_period = $_SESSION['paymentMethod'];


        View::renderTemplate('PersonalBudget/successAreYouSureDeleteFromIncomes.html', [
            'user' => $this->user,
            'id_incomes_delete' => $id_incomes_delete,
            'data_to_are_you_sure_table_incomes' => $data_to_are_you_sure_table_incomes,
            'ordinal_delete_incomes_number' => $ordinal_delete_incomes_number,
            'which_period' => $which_period,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function successAreyouSuredeleteFromExpensesAction()
    {
        $id_expenses_delete = (int)$this->route_params['idexpensesdelete'];
        $data_to_are_you_sure_table_expenses = \App\Models\ModelPersonalBudget::selectAllFromExpensesToEdit($id_expenses_delete);
        if (!$data_to_are_you_sure_table_expenses) {
            Flash::addMessage('Nie znaleziono id lub nie należy do Twojego konta', Flash::WARNING);
            $this->redirect('/personalbudget/browsethebalance');
        }
        $ordinal_delete_expenses_number = (int)$this->route_params['myordinalnumberdeleteexpensesvar'];
        $which_period = $_SESSION['paymentMethod'];

        View::renderTemplate('PersonalBudget/successAreYouSureDeleteFromExpenses.html', [
            'user' => $this->user,
            'id_expenses_delete' => $id_expenses_delete,
            'data_to_are_you_sure_table_expenses' => $data_to_are_you_sure_table_expenses,
            'ordinal_delete_expenses_number' => $ordinal_delete_expenses_number,
            'which_period' => $which_period,
            'csrf_token' => Csrf::generate()
        ]);
    }    

    public function redirectToChosenPeriod(){
        if($_SESSION['paymentMethod'] == "currentMonth"){
            $this->redirect('/personalbudget/successbrowseselectedperiodcurrentmonth');
        } else if ($_SESSION['paymentMethod'] == "currentYear"){         
            $this->redirect('/personalbudget/successbrowseselectedperiodcurrentyear');
        } else if ($_SESSION['paymentMethod'] == "lastMonth"){         
            $this->redirect('/personalbudget/successbrowseselectedperiodlastmonth');
        } else {
            $this->redirect('/personalbudget/successselectedperiodchoosethedate');
        }
    }

    public function updateIncomeAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $personalBudget = new ModelPersonalBudget($_POST);
        $result = $personalBudget->updateIncomes();

        if ($result === true) {
            Flash::addMessage('Pomyślnie zakończono edycję');
            $this->redirectToChosenPeriod();
        }

        Flash::addFormData('update_income_form', $_POST);


        foreach ($result as $error) {
            Flash::addMessage($error, Flash::WARNING);
        }

        $this->redirect('/personalbudget/editIncomes');
    }

    public function updateExpenseAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }

        $personalBudget = new ModelPersonalBudget($_POST);
        $result = $personalBudget->updateExpenses();

        if ($result === true) {
            Flash::addMessage('Pomyślnie zakończono edycję');
            $this->redirectToChosenPeriod();
        }

        Flash::addFormData('update_expense_form', $_POST);

        foreach ($result as $error) {
            Flash::addMessage($error, Flash::WARNING);
        }

        $this->redirect('/personalbudget/editExpenses');
    }

    public function editIncomes()
    {
        if (isset($_POST['editRowIncomes'])) {
            if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
                $this->redirect('/');
            }
            $idIncomesEditRow = filter_input(INPUT_POST, 'editRowIncomes', FILTER_VALIDATE_INT);
            if (!$idIncomesEditRow) {
                $this->redirect('/personalbudget/browsethebalance');
            }
 
            $incomesEditValues = \App\Models\ModelPersonalBudget::selectAllFromIncomesToEdit($idIncomesEditRow);
        }
           else {
                    $incomesEditValues = Flash::getFormData('update_income_form');
           }
        $incomes_options_form = \App\Models\ModelPersonalBudget::selectOptionsForIncomes();

        View::renderTemplate('PersonalBudget/editIncome.html', [
            'user' => $this->user,
            'incomes_edit_values' => $incomesEditValues,
            'incomes_options_form' => $incomes_options_form,
            'csrf_token' => Csrf::generate()
        ]);
    }


    public function editExpenses()
    {

        if (isset($_POST['editRow'])) {
            if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
                $this->redirect('/');
            }
            $idExpensesEditRow = filter_input(INPUT_POST, 'editRow', FILTER_VALIDATE_INT);
            if (!$idExpensesEditRow) {
                $this->redirect('/personalbudget/browsethebalance');
            }

            $expensesEditValues = \App\Models\ModelPersonalBudget::selectAllFromExpensesToEdit($idExpensesEditRow);     
        }

        else {
            $expensesEditValues = Flash::getFormData('update_expense_form');
           } 
        $expenses_options_form_category = \App\Models\ModelPersonalBudget::selectOptionsForExpensesCategory();           
        $expenses_options_form_payment_method = \App\Models\ModelPersonalBudget::selectOptionsForExpensesPaymentMethod();

        View::renderTemplate('PersonalBudget/editExpense.html', [
            'user' => $this->user,
            'expenses_edit_values' => $expensesEditValues,
            'expenses_options_form_category' => $expenses_options_form_category,
            'expenses_options_form_payment_method' => $expenses_options_form_payment_method,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function areYouSureDeleteFromIncomes()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $idincomesdelete = filter_input(INPUT_POST, 'deleteRowIncomes', FILTER_VALIDATE_INT);
        if (!$idincomesdelete) {
            $this->redirect('/personalbudget/browsethebalance');
        }
        
        $myordinalnumberdeleteincomesvar = filter_input(INPUT_POST, 'myOrdinalNumberDeleteIncomes', FILTER_VALIDATE_INT);
        if (!$myordinalnumberdeleteincomesvar) {
            $this->redirect('/personalbudget/browsethebalance');
        }
        $this->redirect('/personalbudget/successareyousuredeletefromincomes/' . $idincomesdelete . '/' . $myordinalnumberdeleteincomesvar);        
    }

    public function areYouSureDeleteFromExpenses()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $idexpensesdelete = filter_input(INPUT_POST, 'deleteRow', FILTER_VALIDATE_INT);
        if (!$idexpensesdelete) {
            $this->redirect('/personalbudget/browsethebalance');
        }
        
        $myordinalnumberdeleteexpensesvar = filter_input(INPUT_POST, 'myOrdinalNumberDeleteExpenses', FILTER_VALIDATE_INT);
        if (!$myordinalnumberdeleteexpensesvar) {
            $this->redirect('/personalbudget/browsethebalance');
        }

        $this->redirect('/personalbudget/successareyousuredeletefromexpenses/' . $idexpensesdelete . '/' . $myordinalnumberdeleteexpensesvar); 
    }

    public function deleteFromIncomes()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $idIncomesDelete = filter_input(INPUT_POST, 'deleteRow', FILTER_VALIDATE_INT);
        if (!$idIncomesDelete) {
            $this->redirect('/personalbudget/browsethebalance');
        }
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->deleteIncome($idIncomesDelete)) {
            Flash::addMessage('Pomyślnie usunięto rekord');
            $this->redirectToChosenPeriod();
         }
    }

    public function deleteFromExpenses()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $idExpensesDelete = filter_input(INPUT_POST, 'deleteRow', FILTER_VALIDATE_INT);
        if (!$idExpensesDelete) {
            $this->redirect('/personalbudget/browsethebalance');
        }
        $personalBudget = new ModelPersonalBudget($_POST);
        if ($personalBudget->deleteExpense($idExpensesDelete)) {
            Flash::addMessage('Pomyślnie usunięto rekord');
            $this->redirectToChosenPeriod();
        }
    }

    public function newIncomeAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }   

        $personalBudget = new ModelPersonalBudget($_POST);
        $result = $personalBudget->insertToIncomes();

        if ($result === true) {
            $this->redirect('/personalbudget/successaddincome');
        }

        Flash::addFormData('new_income_form', $_POST);

        foreach ($result as $error) {
            Flash::addMessage($error, Flash::WARNING);
        }

        $this->redirect('/personalbudget/addincome');
    }

    public function newExpenseAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
            $personalBudget = new ModelPersonalBudget($_POST);
            $result = $personalBudget->insertToExpenses();

            if ($result === true) {
                $this->redirect('/personalbudget/successaddexpense');
            }

            Flash::addFormData('new_expense_form', $_POST);

            foreach ($result as $error) {
                Flash::addMessage($error, Flash::WARNING);
            }

            $this->redirect('/personalbudget/addexpense');
    }

    public static function dateFromToCurrentMonth()
    {
        $dataHelpYearMonth = date("Y-m");
        $currentDate=date("Y-m-d");

        $dateFromTo = $dataHelpYearMonth."-01 do ".$currentDate; 

        return $dateFromTo;
    }

    public static function dateFromToLastMonth()
    {
        $timeYear = date('Y', strtotime("-1 MONTH"));
        $timeMonth = date('m', strtotime("-1 MONTH"));
        $timeHowManyDays = date('t', strtotime("-1 MONTH"));

        $dateFromTo = $timeYear."-".$timeMonth."-01 do ".$timeYear."-".$timeMonth."-".$timeHowManyDays;

        return $dateFromTo;
    }
    
    public static function dateFromToCurrentYear()
    {
        $dateCurrentYear = date("Y");
        $currentDate=date("Y-m-d");
        $dateFromTo = 	$dateCurrentYear."-01-01 do ".$currentDate;

        return $dateFromTo;
    }

    public static function dateFromToSelectedPeriodDate1()
    {
        $dateSelectedPeriod1 = $_POST['dateSelectedPeriod1'];

        return $dateSelectedPeriod1;
    }

    public static function dateFromToSelectedPeriodDate2()
    {
        $dateSelectedPeriod2 = $_POST['dateSelectedPeriod2'];

        return $dateSelectedPeriod2;
    } 

    public function newBrowseTheBalanceAction()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $paymentMethod = $_POST['paymentMethod'];
        $_SESSION['paymentMethod'] = $paymentMethod;

        if($paymentMethod=='currentMonth')
            {
                $this->redirect('/personalbudget/successbrowseselectedperiodcurrentmonth');
            }
        elseif($paymentMethod=='lastMonth'){
            {
                $this->redirect('/personalbudget/successbrowseselectedperiodlastmonth');
            }
        }
        elseif ($paymentMethod=='currentYear'){
            {
                $this->redirect('/personalbudget/successbrowseselectedperiodcurrentyear');      
            }
        }
        elseif ($paymentMethod=='selectedPeriod'){
            $this->redirect('/personalbudget/browseselectedperiodprocessing');
        }
    } 
    
    public function newSelectedPeriod()
    {
        if (!Csrf::verify($_POST['csrf_token'] ?? '')) {
            $this->redirect('/');
        }
        $dateSelectedPeriod1 = $_POST['dateSelectedPeriod1'];
        $dateSelectedPeriod2 = $_POST['dateSelectedPeriod2'];

        $_SESSION['selectedPeriod'] = "selectedPeriod";
        $_SESSION['start_date'] = $dateSelectedPeriod1;
        $_SESSION['end_date'] = $dateSelectedPeriod2;

        if($_SESSION['start_date']>$_SESSION['end_date']){
            $this->redirect('/personalbudget/choosecorrectdate');
        } else {
            $this->redirect('/personalbudget/successselectedperiodchoosethedate');  
        }      
    }

    public function dateLimitSumExpenseAction()
    {
        $category = $this->route_params['category'];
        $year     = $this->route_params['year'];
        $month    = $this->route_params['month'];

        $personalBudget = new ModelPersonalBudget();

        $sum = $personalBudget->selectSumOfExpensesForParticularCategoryAndDate(
            $category,
            $year,
            $month
        );

        echo json_encode(
            ['category' => $category, 'year' => $year, 'month' => $month, 'total' => $sum],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function limitAction()
    {
        $category = $this->route_params['category'];

        $personalBudget = new ModelPersonalBudget();

        $limit = $personalBudget->selectLimitValueUserIdCategoryName($category);

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode(
            ['limit' => $limit],
            JSON_UNESCAPED_UNICODE
        );
    }

    public function choosecorrectdateAction()
    {
        View::renderTemplate('PersonalBudget/chooseCorrectDate.html', [
            'user' => $this->user
        ]);
    }

    public function getAdviceAjaxAction()
    {
        $dateCurrentMonth = \App\Models\ModelPersonalBudget::getDateCurrentMonth();
        $incomes = \App\Models\ModelPersonalBudget::sumOfNamesFromIncomesToChart($dateCurrentMonth);
        $expenses = \App\Models\ModelPersonalBudget::sumOfNamesFromExpensesToChart($dateCurrentMonth);

        $advice = self::generateFinancialAdvice($incomes, $expenses);

        echo $advice; // zwracamy tylko tekst
    }

    public static function sumIncomesAndExpensesForGeminiPrompt($incomesSum, $expensesSum)
    {
        $totalIncome = 0;
        foreach ($incomesSum as $row) {
            $totalIncome += $row['incNameSum'];
            $totalIncome = number_format($totalIncome, 2, '.', '');
        }

        $totalExpense = 0;
        foreach ($expensesSum as $row) {
            $totalExpense += $row['expNameSum']; // jeśli masz expNameSum
            $totalExpense = number_format($totalExpense, 2, '.', '');
        }

        $incomeText = "";
        foreach ($incomesSum as $row) {
            $incomeText .= $row['catName'] . ": " . $row['incNameSum'] . " zł\n";
        }

        $expenseText = "";
        foreach ($expensesSum as $row) {
            $expenseText .= $row['catName'] . ": " . $row['expNameSum'] . " zł\n";
        }

        // $client = new \GeminiAPI\Client(\App\Config::GEMINI_API_KEY());

        $prompt = "
        Jako doradca finansowy oceń sytuację użytkownika. 
        Przeanalizuj na co dana osoba wydaje pieniądze w jaki sposób je zarabia.
        Wynik zwróć w czystym HTML — bez Markdown, bez **, bez ###.
        Cały tekst ma być czarny: używaj <div style='color:#000;'> ... </div>.

        <div style='color:#000;'>

        Wyświetl poniższe informacje:
        <strong>Suma przychodów:</strong> {$totalIncome} zł <br><br>
        <strong> Suma wydatków:</strong> {$totalExpense} zł zrób odstęp <br><br>

        <strong>Przychody:</strong>
        {$incomeText}
        <br><br>

        <strong>Wydatki:</strong>
        {$expenseText}
        <br><br>

        Doradź w jaki sposób ta osoba może mądrze i lepiej zarządzać swoimi finansami .
        </div>";

        // echo '<pre>' . $prompt . '</pre>';
        // exit;

        return $prompt;
    }

    public static function generateFinancialAdvice($incomesSum, $expensesSum)
    {
        $apiKey = \App\Config::GEMINI_API_KEY();
        $prompt = \App\Controllers\Personalbudget::sumIncomesAndExpensesForGeminiPrompt($incomesSum, $expensesSum);

        $data = [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ];

        // $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.7-flash:generateContent?key=$apiKey";
        // $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$apiKey";
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash-lite:generateContent?key=$apiKey";
        $ch = curl_init($url);

        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_RETURNTRANSFER => true
        ]);

        $response = curl_exec($ch);
        // echo "<pre>";
        // var_dump($response);
        // echo "</pre>";
        // exit;


        curl_close($ch);

        $json = json_decode($response, true);

        return $json["candidates"][0]["content"]["parts"][0]["text"]
            ?? "Brak odpowiedzi od Gemini.";
    }



    // public static function generateFinancialAdvice($incomesSum, $expensesSum)
    // {
    //     $apiKey = \App\Config::GEMINI_API_KEY();
    //     $prompt = \App\Controllers\Personalbudget::sumIncomesAndExpensesForGeminiPrompt($incomesSum, $expensesSum);


    //     $client = new \GeminiAPI\Client($apiKey);

    //     $response = $client->withV1Version()   // ← KLUCZOWA ZMIANA
    //         ->generativeModel(\GeminiAPI\Resources\ModelName::GEMINI_1_5_FLASH)
    //         ->generateContent(
    //             new \GeminiAPI\Resources\Parts\TextPart($prompt)
    //         );

    //     return $response->text();

        // $data = [
        //     "model" => "gemini-3.7-flash",
        //     "input" => [
        //         [
        //             "role" => "user",
        //             "content" => $prompt
        //         ]
        //     ]
        // ];

        // $ch = curl_init("https://api.gemini.google.com/v1beta/models/gemini-3.7-flash:generateContent");

        // curl_setopt_array($ch, [
        //     CURLOPT_POST => true,
        //     CURLOPT_HTTPHEADER => [
        //         "Content-Type: application/json",
        //         "x-goog-api-key: $apiKey"
        //     ],
        //     CURLOPT_POSTFIELDS => json_encode($data),
        //     CURLOPT_RETURNTRANSFER => true
        // ]);

        // $response = curl_exec($ch);
        // curl_close($ch);

        // $json = json_decode($response, true);

        // return $json["output_text"] ?? "Brak odpowiedzi od Gemini.";
    // }


    //     public static function generateFinancialAdvice($incomesSum, $expensesSum)
    // {  
    //     $response = $client->withV1BetaVersion()
    //         ->generativeModel(\GeminiAPI\Resources\ModelName::GEMINI_1_5_FLASH)
    //         ->generateContent(
    //             new \GeminiAPI\Resources\Parts\TextPart($prompt)
    //         );

    //     return $response->text();
    // }

    public function successBrowseSelectedPeriodCurrentMonthAction()
    {
        $dateCurrentMonth = \App\Models\ModelPersonalBudget::getDateCurrentMonth();        
        $date_from_to_current_month = \App\Controllers\Personalbudget::dateFromToCurrentMonth();
        $query_name_income_current_month = \App\Models\ModelPersonalBudget::getQueryNameIncome($dateCurrentMonth);
        $query_name_expense_current_month = \App\Models\ModelPersonalBudget::getQueryNameExpense($dateCurrentMonth);
        $query_name_incomes_sum_current_month = \App\Models\ModelPersonalBudget::incomesSum($dateCurrentMonth);
        $query_name_expenses_sum_current_month = \App\Models\ModelPersonalBudget::expensesSum($dateCurrentMonth);
        $chart_incomes_current_month = \App\Models\ModelPersonalBudget::sumOfNamesFromIncomesToChart($dateCurrentMonth);
        $chart_expenses_current_month = \App\Models\ModelPersonalBudget::sumOfNamesFromExpensesToChart($dateCurrentMonth);

        // echo '<pre>';
        // print_r($chart_expenses_current_month);
        // echo '</pre>';
        // exit;


        // $financial_advice = \App\Controllers\Personalbudget::generateFinancialAdvice(
        //     $chart_incomes_current_month,
        //     $chart_expenses_current_month
        // );

        View::renderTemplate('PersonalBudget/browseSelectedPeriodCurrentMonth.html', [
            'user' => $this->user,
            'date_from_to_current_month' => $date_from_to_current_month,
            'query_name_income_current_month' => $query_name_income_current_month,
            'query_name_expense_current_month' => $query_name_expense_current_month,
            'query_name_incomes_sum_current_month' => $query_name_incomes_sum_current_month,
            'query_name_expenses_sum_current_month' => $query_name_expenses_sum_current_month,
            'chart_incomes_current_month' => $chart_incomes_current_month,
            'chart_expenses_current_month' => $chart_expenses_current_month,
            // 'financial_advice' => $financial_advice,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function successBrowseSelectedPeriodLastMonthAction()
    {
        $dateLastMonth = \App\Models\ModelPersonalBudget::getDateLastMonth();

            $date_from_to_last_month = \App\Controllers\Personalbudget::dateFromToLastMonth();
            $query_name_income_last_month = \App\Models\ModelPersonalBudget::getQueryNameIncome($dateLastMonth);
            $query_name_expense_last_month = \App\Models\ModelPersonalBudget::getQueryNameExpense($dateLastMonth);
            $query_name_incomes_sum_last_month = \App\Models\ModelPersonalBudget::incomesSum($dateLastMonth);
            $query_name_expenses_sum_last_month = \App\Models\ModelPersonalBudget::expensesSum($dateLastMonth);
            $chart_incomes_last_month = \App\Models\ModelPersonalBudget::sumOfNamesFromIncomesToChart($dateLastMonth);
            $chart_expenses_last_month = \App\Models\ModelPersonalBudget::sumOfNamesFromExpensesToChart($dateLastMonth);

            View::renderTemplate('PersonalBudget/browseSelectedPeriodLastMonth.html', [
                'user' => $this->user,
                'date_from_to_last_month' => $date_from_to_last_month,
                'query_name_income_last_month' => $query_name_income_last_month,
                'query_name_expense_last_month' => $query_name_expense_last_month,
                'query_name_incomes_sum_last_month' => $query_name_incomes_sum_last_month,
                'query_name_expenses_sum_last_month' => $query_name_expenses_sum_last_month,
                'chart_incomes_last_month' => $chart_incomes_last_month,
                'chart_expenses_last_month' => $chart_expenses_last_month,
                'csrf_token' => Csrf::generate()
        ]);
    }

    public function successBrowseSelectedPeriodCurrentYearAction()
    {
        $dateCurrentYear = \App\Models\ModelPersonalBudget::getDateCurrentYear();

        $date_from_to_current_year = \App\Controllers\Personalbudget::dateFromToCurrentYear();
        $query_name_income_current_year = \App\Models\ModelPersonalBudget::getQueryNameIncome($dateCurrentYear);
        $query_name_expense_current_year = \App\Models\ModelPersonalBudget::getQueryNameExpense($dateCurrentYear);
        $query_name_incomes_sum_current_year = \App\Models\ModelPersonalBudget::incomesSum($dateCurrentYear);
        $query_name_expenses_sum_current_year = \App\Models\ModelPersonalBudget::expensesSum($dateCurrentYear);
        $chart_incomes_current_year = \App\Models\ModelPersonalBudget::sumOfNamesFromIncomesToChart($dateCurrentYear);
        $chart_expenses_current_year = \App\Models\ModelPersonalBudget::sumOfNamesFromExpensesToChart($dateCurrentYear);

        View::renderTemplate('PersonalBudget/browseSelectedPeriodCurrentYear.html', [
            'user' => $this->user,
            'date_from_to_current_year' => $date_from_to_current_year,
            'query_name_income_current_year' => $query_name_income_current_year,
            'query_name_expense_current_year' => $query_name_expense_current_year,
            'query_name_incomes_sum_current_year' => $query_name_incomes_sum_current_year,
            'query_name_expenses_sum_current_year' => $query_name_expenses_sum_current_year,
            'chart_incomes_current_year' => $chart_incomes_current_year,
            'chart_expenses_current_year' => $chart_expenses_current_year,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function successSelectedPeriodChooseTheDateAction()
    {
        $start_date_selected_period = \App\Models\ModelPersonalBudget::getStartDateSelectedPeriod(); 
        $end_date_selected_period = \App\Models\ModelPersonalBudget::getEndDateSelectedPeriod(); 

        $query_name_incomes_selected_period = \App\Models\ModelPersonalBudget::getSelectedPeriodQueryNameIncome();
        $query_name_expenses_selected_period = \App\Models\ModelPersonalBudget::getSelectedPeriodQueryNameExpense();
        $query_name_incomes_sum_selected_period = \App\Models\ModelPersonalBudget::incomesSelectedPeriodSum();
        $query_name_expenses_sum_selected_period = \App\Models\ModelPersonalBudget::expensesSelectedPeriodSum();
        $chart_incomes_selected_period = \App\Models\ModelPersonalBudget::sumOfNamesFromIncomesToChartSelectedPeriod();
        $chart_expenses_selected_period = \App\Models\ModelPersonalBudget::sumOfNamesFromExpensesToChartSelectedPeriod();

        View::renderTemplate('PersonalBudget/successSelectedPeriodChooseTheDate.html', [
            'user' => $this->user,
            'start_date_selected_period' => $start_date_selected_period,
            'end_date_selected_period' => $end_date_selected_period,
            'query_name_incomes_selected_period' => $query_name_incomes_selected_period,
            'query_name_expenses_selected_period' => $query_name_expenses_selected_period,
            'query_name_incomes_sum_selected_period' => $query_name_incomes_sum_selected_period,
            'query_name_expenses_sum_selected_period' => $query_name_expenses_sum_selected_period,
            'chart_incomes_selected_period' => $chart_incomes_selected_period,
            'chart_expenses_selected_period' => $chart_expenses_selected_period,
            'csrf_token' => Csrf::generate()
        ]);
    }

    public function browseselectedperiodprocessingAction()
    {
        View::renderTemplate('PersonalBudget/browseSelectedPeriodProcessingChooseTheDate.html', [
            'user' => $this->user,
            'csrf_token' => Csrf::generate()
        ]);
    }
    

    public function successAddIncomeAction()
    {
        View::renderTemplate('PersonalBudget/successAddIncome.html', [
            'user' => $this->user
        ]);
    }

    public function successAddExpenseAction()
    {
        View::renderTemplate('PersonalBudget/successAddExpense.html', [
            'user' => $this->user
        ]);
    }
    
}
