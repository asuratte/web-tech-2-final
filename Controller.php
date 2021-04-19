<?php

require_once './model/Database.php';
require_once './model/Validator.php';
require_once './model/MealPlansTable.php';
require_once 'autoload.php';

class Controller {

    private $action;
    private $db;
    private $meal_plans_table;
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
        echo $template->render(['log_in_message' => $log_in_error_message, 'log_in_success_message' => $log_in_success_message]);
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
