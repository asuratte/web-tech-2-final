<?php

require_once './model/Database.php';
require_once './model/Validator.php';
require_once './model/MealPlansTable.php';
require_once './model/StatesTable.php';
require_once './model/CustomersTable.php';
require_once 'autoload.php';

class Controller {

    private $action;
    private $db;
    private $meal_plans_table;
    private $states_table;
    private $customers_table;
    private $twig;

    /**
     * Instantiates a new controller
     */
    public function __construct() {
        $loader = new Twig\Loader\FilesystemLoader('./view');
        $this->twig = new Twig\Environment($loader);
        $this->setSecureConnectionAndSession();
        $this->connectToDatabase();
        if ($this->db->isConnected()) {
            $this->meal_plans_table = new MealPlansTable($this->db);
            $this->states_table = new StatesTable($this->db);
            $this->customers_table = new CustomersTable($this->db);
            $this->validator = new Validator($this->db, $this->customers_table);
        } else {
            $error_message = $this->db->getErrorMessage();
            $template = $this->twig->load('database_error.twig');
            exit();
        }
        $this->action = $this->getAction();
    }

    /**
     * Initiates processing of the current action
     */
    public function invoke() {
        switch ($this->action) {
            case 'Show Home':
                $this->processShowHomePage();
                break;
            case 'Show FAQ':
                $this->processShowFAQPage();
                break;
            case 'Show Meal Options':
                $this->processShowMealOptionsPage();
                break;
            case 'Show Log In':
                $this->processShowLogInPage();
                break;
            case 'Log in':
                $this->processLogIn();
                break;
            case 'Log Out':
                $this->processLogOut();
                break;
            case 'Show Sign Up':
                $this->processShowSignUpPage();
                break;
            case 'Sign up':
                $this->processSignUp();
                break;
            default:
                $this->processShowHomePage();
                break;
        }
    }

    /**
     * Process requests
     */
    private function processShowHomePage() {
        $template = $this->twig->load('home.twig');
        echo $template->render();
    }

    private function processShowFAQPage() {
        $template = $this->twig->load('faq.twig');
        echo $template->render();
    }

    private function processShowMealOptionsPage() {
        $meal_plans = $this->meal_plans_table->get_meal_plans();
        $template = $this->twig->load('meal_options.twig');
        echo $template->render(['meal_plans' => $meal_plans]);
    }

    private function processShowLogInPage() {
        $log_in_error_message = '';
        $log_in_success_message = '';
        $template = $this->twig->load('log_in.twig');
        echo $template->render(['log_in_error_message' => $log_in_error_message, 'log_in_success_message' => $log_in_success_message]);
    }

    private function processLogIn() {
        $username = filter_input(INPUT_POST, 'username');
        $password = filter_input(INPUT_POST, 'password');
        if ($this->db->isValidUserLogIn($username, $password)) {
            $_SESSION['is_valid_user'] = true;
            $_SESSION['username'] = $username;
            $log_in_success_message = 'You are logged in as ' + $username + '.';
            $log_in_error_message = '';
            $template = $this->twig->load('log_in.twig');
            echo $template->render(['username' => $username, 'log_in_error_message' => $log_in_error_message, 'log_in_success_message' => $log_in_success_message]);
        } else {
            $log_in_error_message = 'Invalid username or password.';
            $log_in_success_message = '';
            $template = $this->twig->load('log_in.twig');
            echo $template->render(['log_in_error_message' => $log_in_error_message, 'log_in_success_message' => $log_in_success_message]);
        }
    }

    private function processLogOut() {
        $_SESSION = array();
        session_destroy();
        $this->twig->addGlobal('session', $_SESSION);
        $log_in_success_message = 'You have been logged out.';
        $template = $this->twig->load('log_in.twig');
        echo $template->render(['log_in_success_message' => $log_in_success_message]);
    }

    private function processShowSignUpPage() {
        $meal_plans = $this->meal_plans_table->get_meal_plans();
        $states = $this->states_table->get_states();
        $sign_up_error_message = '';
        $sign_up_success_message = '';
        $template = $this->twig->load('sign_up.twig');
        echo $template->render(['sign_up_error_message' => $sign_up_error_message, 'sign_up_success_message' => $sign_up_success_message,
            'meal_plans' => $meal_plans, 'states' => $states]);
    }

    private function processSignUp() {
        // get input from form
        $first_name = filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $last_name = filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $street_address = filter_input(INPUT_POST, 'streetAddress', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $state_field = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $zip_code = filter_input(INPUT_POST, 'zipCode', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $phone = filter_input(INPUT_POST, 'phoneNumber', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $dietary_preference = filter_input(INPUT_POST, 'dietaryPreference', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $confirm_password = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // check required fields with validator
        $error_first_name = $this->validator->checkTextField($first_name);
        $error_last_name = $this->validator->checkTextField($last_name);
        $error_street_address = $this->validator->checkTextField($street_address);
        $error_city = $this->validator->checkTextField($city);
        $error_state_field = $this->validator->checkTextField($state_field);
        $error_zip_code = $this->validator->checkTextField($zip_code, true, 1, 21);
        $error_email = $this->validator->checkEmail($email);
        $error_phone = $this->validator->checkPhone($phone);
        $error_username = $this->validator->checkUsername($username);
        //$this->validator->checkPassword($password);
        //$this->validator->checkPasswordsMatch($password, $confirm_password);
        // if validator comes back with errors
        if (!empty($error_first_name) || !empty($error_last_name) || !empty($error_street_address) || !empty($error_city) || !empty($error_state_field) 
                || !empty($error_zip_code) || !empty($error_username) || !empty($error_phone) || !empty($error_phone)) {
            $this->processShowSignUpErrors($first_name, $error_first_name,
                    $last_name, $error_last_name, $street_address, $error_street_address, $city, $error_city, $state_field, $error_state_field, $zip_code,
                    $error_zip_code, $username, $error_username, $phone, $error_phone, $email, $error_email);
        } else {
            $this->processAddCustomerToDatabase($first_name, $error_first_name,
            $last_name, $error_last_name, $street_address, $error_street_address, $city, $error_city, $state_field, $error_state_field, $zip_code,
            $error_zip_code, $username, $error_username, $phone, $error_phone, $email, $error_email);
        }
    }

    private function processShowSignUpErrors($first_name, $error_first_name,
            $last_name, $error_last_name, $street_address, $error_street_address, $city, $error_city, $state_field, $error_state_field, $zip_code,
            $error_zip_code, $username, $error_username, $phone, $error_phone, $email, $error_email) {
        $states = $this->states_table->get_states();
        $meal_plans = $this->meal_plans_table->get_meal_plans();
        $sign_up_error_message = 'There was a problem with your submission. Please resolve the issues listed above and try again.';
        $sign_up_success_message = '';
        $template = $this->twig->load('sign_up.twig');
        echo $template->render(['sign_up_error_message' => $sign_up_error_message, 'sign_up_success_message' => $sign_up_success_message,
            'meal_plans' => $meal_plans, 'states' => $states, 'first_name' => $first_name, 'error_first_name' => $error_first_name,
            'last_name' => $last_name, 'error_last_name' => $error_last_name, 'street_address' => $street_address, 'error_street_address' => $error_street_address,
            'city' => $city, 'error_city' => $error_city, 'state_field' => $state_field, 'error_state_field' => $error_state_field,
            'zip_code' => $zip_code, 'error_zip_code' => $error_zip_code, 'username' => $username, 'error_username' => $error_username, 'phone' => $phone,
                'error_phone' => $error_phone, 'email' => $email,
                'error_email' => $error_email]);
    }

    private function processAddCustomerToDatabase($first_name, $error_first_name,
            $last_name, $error_last_name, $street_address, $error_street_address, $city, $error_city, $state_field, $error_state_field, $zip_code,
            $error_zip_code, $username, $error_username, $phone, $error_phone) {
        $states = $this->states_table->get_states();
        $meal_plans = $this->meal_plans_table->get_meal_plans();
        $sign_up_error_message = '';
        $sign_up_success_message = 'You have successfully created an account.';
        /** $hash = password_hash($password, PASSWORD_DEFAULT);
          $customers_table = new CustomersTable($this->db);
          $customers_table->add_customer($first_name, $last_name,
          $street_address, $city, $state, $zip_code, $phone, $email,
          $dietary_preference, $username, $hash);
        **/
        $template = $this->twig->load('sign_up.twig');
        echo $template->render(['sign_up_error_message' => $sign_up_error_message, 'sign_up_success_message' => $sign_up_success_message,
            'meal_plans' => $meal_plans, 'states' => $states, 'first_name' => $first_name, 'error_first_name' => $error_first_name,
            'last_name' => $last_name, 'error_last_name' => $error_last_name, 'street_address' => $street_address, 'error_street_address' => $error_street_address,
            'city' => $city, 'error_city' => $error_city, 'state_field' => $state_field, 'error_state_field' => $error_state_field,
            'zip_code' => $zip_code, 'error_zip_code' => $error_zip_code, 'username' => $username, 'error_username' => $error_username, 'phone' => $phone,
                'error_phone' => $error_phone]);
    }

    /**
     * Gets the action from $_GET or $_POST array
     * 
     * @return string the action to be processed
     */
    private function getAction() {
        $action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        if ($action === NULL) {
            $action = filter_input(INPUT_GET, 'action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if ($action === NULL) {
                $action = '';
            }
        }
        return $action;
    }

    /**
     * Sets up HTTPS connection and session
     */
    private function setSecureConnectionAndSession() {
        $https = filter_input(INPUT_SERVER, 'HTTPS');
        if (!$https) {
            $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
            $uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
            $url = 'https://' . $host . $uri;
            header("Location: " . $url);
            exit();
        }
        session_start();
        $this->twig->addGlobal('session', $_SESSION);
    }

    /**
     * Connects to the database
     */
    private function connectToDatabase() {
        $this->db = new Database();
        if (!$this->db->isConnected()) {
            $error_message = $this->db->getErrorMessage();
            $template = $this->twig->load('database_error.twig');
            echo $template->render(['error_message' => $error_message]);
            exit();
        }
    }

}
