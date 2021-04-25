<?php

require_once './model/Database.php';
require_once './model/Validator.php';
require_once './model/MealPlansTable.php';
require_once './model/StatesTable.php';
require_once './model/CustomersTable.php';
require_once './model/AddOnsTable.php';
require_once './model/ZipCodesTable.php';
require_once './model/OrdersTable.php';
require_once 'autoload.php';

class Controller {

    private $action;
    private $db;
    private $meal_plans_table;
    private $states_table;
    private $customers_table;
    private $add_ons_table;
    private $zip_codes_table;
    private $orders_table;
    private $twig;

    /**
     * Instantiates a new controller
     */
    public function __construct() {
        $loader = new Twig\Loader\FilesystemLoader('./view');
        $this->twig = new Twig\Environment($loader);
        $this->setSecureConnectionAndSession();
        $this->connectToDatabase();
        $this->meal_plans_table = new MealPlansTable($this->db);
        $this->add_ons_table = new AddOnsTable($this->db);
        $this->zip_codes_table = new ZipCodesTable($this->db);
        $this->states_table = new StatesTable($this->db);
        $this->customers_table = new CustomersTable($this->db);
        $this->orders_table = new OrdersTable($this->db);
        $this->validator = new Validator($this->db, $this->customers_table);
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
            case 'Show My Account':
                $this->processShowMyAccountPage();
                break;
            case 'Show Order History':
                $this->processShowOrderHistoryPage();
                break;
            case 'Show Order Now':
                $this->processShowOrderNowPage();
                break;
            case 'Calculate total':
                $this->processCalculateOrderTotal();
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
        $add_ons = $this->add_ons_table->get_add_ons();
        $template = $this->twig->load('meal_options.twig');
        echo $template->render(['meal_plans' => $meal_plans, 'add_ons' => $add_ons]);
    }

    private function processShowLogInPage() {
        $log_in_error_message = '';
        $log_in_success_message = '';
        $template = $this->twig->load('log_in.twig');
        echo $template->render(['log_in_error_message' => $log_in_error_message, 'log_in_success_message' => $log_in_success_message]);
    }

    private function processShowMyAccountPage() {
        $template = $this->twig->load('my_account.twig');
        echo $template->render();
    }

    private function processShowOrderHistoryPage() {
        $template = $this->twig->load('order_history.twig');
        echo $template->render();
    }

    private function processShowOrderNowPage() {
        $order_error_message = '';
        $meal_plans = $this->meal_plans_table->get_meal_plans();
        $add_ons = $this->add_ons_table->get_add_ons();
        $zip_codes = $this->zip_codes_table->get_zip_codes();
        $standard_breakfast_quantity = 0;
        $standard_lunch_quantity = 0;
        $standard_dinner_quantity = 0;
        $gluten_free_breakfast_quantity = 0;
        $gluten_free_lunch_quantity = 0;
        $gluten_free_dinner_quantity = 0;
        $vegetarian_breakfast_quantity = 0;
        $vegetarian_lunch_quantity = 0;
        $vegetarian_dinner_quantity = 0;
        $vegan_breakfast_quantity = 0;
        $vegan_lunch_quantity = 0;
        $vegan_dinner_quantity = 0;
        $keto_breakfast_quantity = 0;
        $keto_lunch_quantity = 0;
        $keto_dinner_quantity = 0;
        $diabetic_breakfast_quantity = 0;
        $diabetic_lunch_quantity = 0;
        $diabetic_dinner_quantity = 0;
        $show_total_table = false;
        $template = $this->twig->load('order_now.twig');
        echo $template->render(['order_error_message' => $order_error_message, 'meal_plans' => $meal_plans, 'add_ons' => $add_ons, 'zip_codes' => $zip_codes, 'show_total_table' => $show_total_table, 'standard_breakfast_quantity' => $standard_breakfast_quantity, 'standard_lunch_quantity' => $standard_lunch_quantity, 'standard_dinner_quantity' => $standard_dinner_quantity,
            'gluten_free_breakfast_quantity' => $gluten_free_breakfast_quantity, 'gluten_free_lunch_quantity' => $gluten_free_lunch_quantity, 'gluten_free_dinner_quantity' => $gluten_free_dinner_quantity,
            'vegetarian_breakfast_quantity' => $vegetarian_breakfast_quantity, 'vegetarian_lunch_quantity' => $vegetarian_lunch_quantity, 'vegetarian_dinner_quantity' => $vegetarian_dinner_quantity,
            'vegan_breakfast_quantity' => $vegan_breakfast_quantity, 'vegan_lunch_quantity' => $vegan_lunch_quantity, 'vegan_dinner_quantity' => $vegan_dinner_quantity,
            'keto_breakfast_quantity' => $keto_breakfast_quantity, 'keto_lunch_quantity' => $keto_lunch_quantity, 'keto_dinner_quantity' => $keto_dinner_quantity,
            'diabetic_breakfast_quantity' => $diabetic_breakfast_quantity, 'diabetic_lunch_quantity' => $diabetic_lunch_quantity, 'diabetic_dinner_quantity' => $diabetic_dinner_quantity]);
    }

    private function processLogIn() {
        $username = filter_input(INPUT_POST, 'username');
        $password = filter_input(INPUT_POST, 'password');
        if ($this->db->isValidUserLogIn($username, $password)) {
            $customer = $this->customers_table->get_customer_by_username($username);
            $first_name = $customer['firstName'];
            $dietary_preference = $customer['dietaryPreference'];
            $customer_id = $customer['customerID'];
            $this->addToSessionGlobal($username, $first_name, $dietary_preference, $customer_id);
            $log_in_success_message = 'You are logged in as ' . $username . '.';
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
        $dietary_preference = filter_input(INPUT_POST, 'dietaryPreference');
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $confirm_password = filter_input(INPUT_POST, 'confirmPassword', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // check required fields with validator
        $error_first_name = $this->validator->checkTextField($first_name);
        $error_last_name = $this->validator->checkTextField($last_name);
        $error_street_address = $this->validator->checkTextField($street_address);
        $error_city = $this->validator->checkTextField($city);
        $error_state_field = $this->validator->checkTextField($state_field);
        $error_zip_code = $this->validator->checkZipCode($zip_code, true, 1, 21);
        $error_email = $this->validator->checkEmail($email);
        $error_phone = $this->validator->checkPhone($phone);
        $error_username = $this->validator->checkUsername($username);
        $error_password = $this->validator->checkPassword($password);
        $error_confirm_password = $this->validator->checkPasswordsMatch($confirm_password, $password);
        // if validator comes back with errors
        if (!empty($error_first_name) || !empty($error_last_name) || !empty($error_street_address) || !empty($error_city) ||
                !empty($error_state_field) || !empty($error_zip_code) || !empty($error_username) || !empty($error_phone) ||
                !empty($error_phone) || !empty($error_password) || !empty($error_confirm_password)) {
            $this->processShowSignUpErrors($first_name, $error_first_name,
                    $last_name, $error_last_name, $street_address, $error_street_address, $city, $error_city, $state_field, $error_state_field, $zip_code,
                    $error_zip_code, $username, $error_username, $phone, $error_phone, $email, $error_email, $password, $error_password,
                    $confirm_password, $error_confirm_password, $dietary_preference);
        } else {
            $this->processAddCustomerToDatabase($first_name, $error_first_name,
                    $last_name, $error_last_name, $street_address, $error_street_address, $city, $error_city, $state_field, $error_state_field, $zip_code,
                    $error_zip_code, $username, $error_username, $phone, $error_phone, $email, $error_email, $password, $error_password,
                    $confirm_password, $error_confirm_password, $dietary_preference);
        }
    }

    private function processShowSignUpErrors($first_name, $error_first_name,
            $last_name, $error_last_name, $street_address, $error_street_address, $city, $error_city, $state_field, $error_state_field, $zip_code,
            $error_zip_code, $username, $error_username, $phone, $error_phone, $email, $error_email, $password, $error_password,
            $confirm_password, $error_confirm_password, $dietary_preference) {
        $states = $this->states_table->get_states();
        $meal_plans = $this->meal_plans_table->get_meal_plans();
        $sign_up_error_message = 'There was a problem with your submission. Please resolve any errors and try again.';
        $sign_up_success_message = '';
        $template = $this->twig->load('sign_up.twig');
        echo $template->render(['sign_up_error_message' => $sign_up_error_message, 'sign_up_success_message' => $sign_up_success_message,
            'meal_plans' => $meal_plans, 'states' => $states, 'first_name' => $first_name, 'error_first_name' => $error_first_name,
            'last_name' => $last_name, 'error_last_name' => $error_last_name, 'street_address' => $street_address, 'error_street_address' => $error_street_address,
            'city' => $city, 'error_city' => $error_city, 'state_field' => $state_field, 'error_state_field' => $error_state_field,
            'zip_code' => $zip_code, 'error_zip_code' => $error_zip_code, 'username' => $username, 'error_username' => $error_username, 'phone' => $phone,
            'error_phone' => $error_phone, 'email' => $email,
            'error_email' => $error_email, 'password' => $password, 'confirm_password' => $confirm_password, 'error_password' => $error_password,
            'error_confirm_password' => $error_confirm_password, 'dietary_preference' => $dietary_preference]);
    }

    private function processAddCustomerToDatabase($first_name, $error_first_name,
            $last_name, $error_last_name, $street_address, $error_street_address, $city, $error_city, $state_field, $error_state_field, $zip_code,
            $error_zip_code, $username, $error_username, $phone, $error_phone, $email, $error_email, $password, $error_password,
            $confirm_password, $error_confirm_password, $dietary_preference) {
        $states = $this->states_table->get_states();
        $meal_plans = $this->meal_plans_table->get_meal_plans();
        $sign_up_error_message = '';
        $sign_up_success_message = 'You have successfully created an account.';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $customers_table = new CustomersTable($this->db);
        $customers_table->add_customer($first_name, $last_name,
                $street_address, $city, $state_field, $zip_code, $phone, $email,
                $dietary_preference, $username, $hash);
        $customer = $this->customers_table->get_customer_by_username($username);
        $customer_id = $customer['customerID'];
        $this->addToSessionGlobal($username, $first_name, $dietary_preference, $customer_id);
        $template = $this->twig->load('sign_up.twig');
        echo $template->render(['sign_up_error_message' => $sign_up_error_message, 'sign_up_success_message' => $sign_up_success_message,
            'meal_plans' => $meal_plans, 'states' => $states, 'first_name' => $first_name, 'error_first_name' => $error_first_name,
            'last_name' => $last_name, 'error_last_name' => $error_last_name, 'street_address' => $street_address, 'error_street_address' => $error_street_address,
            'city' => $city, 'error_city' => $error_city, 'state_field' => $state_field, 'error_state_field' => $error_state_field,
            'zip_code' => $zip_code, 'error_zip_code' => $error_zip_code, 'username' => $username, 'error_username' => $error_username, 'phone' => $phone,
            'error_phone' => $error_phone, 'email' => $email,
            'error_email' => $error_email, 'password' => $password, 'confirm_password' => $confirm_password, 'error_password' => $error_password,
            'error_confirm_password' => $error_confirm_password, 'dietary_preference' => $dietary_preference]);
    }

    function processCalculateOrderTotal() {
        $order_error_message = '';
        $meal_plans = $this->meal_plans_table->get_meal_plans();
        $add_ons = $this->add_ons_table->get_add_ons();
        $zip_codes = $this->zip_codes_table->get_zip_codes();
        $standard_breakfast_quantity = filter_input(INPUT_POST, 'meal-1-breakfast-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $standard_lunch_quantity = filter_input(INPUT_POST, 'meal-1-lunch-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $standard_dinner_quantity = filter_input(INPUT_POST, 'meal-1-dinner-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $gluten_free_breakfast_quantity = filter_input(INPUT_POST, 'meal-2-breakfast-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $gluten_free_lunch_quantity = filter_input(INPUT_POST, 'meal-2-lunch-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $gluten_free_dinner_quantity = filter_input(INPUT_POST, 'meal-2-dinner-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $vegetarian_breakfast_quantity = filter_input(INPUT_POST, 'meal-3-breakfast-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $vegetarian_lunch_quantity = filter_input(INPUT_POST, 'meal-3-lunch-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $vegetarian_dinner_quantity = filter_input(INPUT_POST, 'meal-3-dinner-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $vegan_breakfast_quantity = filter_input(INPUT_POST, 'meal-4-breakfast-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $vegan_lunch_quantity = filter_input(INPUT_POST, 'meal-4-lunch-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $vegan_dinner_quantity = filter_input(INPUT_POST, 'meal-4-dinner-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $keto_breakfast_quantity = filter_input(INPUT_POST, 'meal-5-breakfast-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $keto_lunch_quantity = filter_input(INPUT_POST, 'meal-5-lunch-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $keto_dinner_quantity = filter_input(INPUT_POST, 'meal-5-dinner-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $diabetic_breakfast_quantity = filter_input(INPUT_POST, 'meal-6-breakfast-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $diabetic_lunch_quantity = filter_input(INPUT_POST, 'meal-6-lunch-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $diabetic_dinner_quantity = filter_input(INPUT_POST, 'meal-6-dinner-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $add_on_juice_quantity = filter_input(INPUT_POST, 'add-on-1-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $add_on_fruit_quantity = filter_input(INPUT_POST, 'add-on-2-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $add_on_hummus_quantity = filter_input(INPUT_POST, 'add-on-3-quantity', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $pickup_or_delivery = filter_input(INPUT_POST, 'pickupOrDelivery', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        // validate form fields 
        $error_standard_breakfast_quantity = $this->validator->checkQuantityField($standard_breakfast_quantity);
        // if validator comes back with errors
        if (!empty($error_standard_breakfast_quantity)) {
            $order_error_message = 'There was a problem with your submission. Please resolve any errors and try again.';
            $template = $this->twig->load('order_now.twig');
            echo $template->render(['order_error_message' => $order_error_message, 'meal_plans' => $meal_plans, 'add_ons' => $add_ons, 'zip_codes' => $zip_codes, 
                'standard_breakfast_quantity' => $standard_breakfast_quantity, 'standard_lunch_quantity' => $standard_lunch_quantity, 'standard_dinner_quantity' => $standard_dinner_quantity,
                'gluten_free_breakfast_quantity' => $gluten_free_breakfast_quantity, 'gluten_free_lunch_quantity' => $gluten_free_lunch_quantity, 'gluten_free_dinner_quantity' => $gluten_free_dinner_quantity,
                'vegetarian_breakfast_quantity' => $vegetarian_breakfast_quantity, 'vegetarian_lunch_quantity' => $vegetarian_lunch_quantity, 'vegetarian_dinner_quantity' => $vegetarian_dinner_quantity,
                'vegan_breakfast_quantity' => $vegan_breakfast_quantity, 'vegan_lunch_quantity' => $vegan_lunch_quantity, 'vegan_dinner_quantity' => $vegan_dinner_quantity,
                'keto_breakfast_quantity' => $keto_breakfast_quantity, 'keto_lunch_quantity' => $keto_lunch_quantity, 'keto_dinner_quantity' => $keto_dinner_quantity,
                'diabetic_breakfast_quantity' => $diabetic_breakfast_quantity, 'diabetic_lunch_quantity' => $diabetic_lunch_quantity, 'diabetic_dinner_quantity' => $diabetic_dinner_quantity]);
        } else {
            $standard_breakfast_subtotal = $this->getMealSubtotal('1', 'breakfast', $standard_breakfast_quantity);
            $standard_lunch_subtotal = $this->getMealSubtotal('1', 'lunch', $standard_lunch_quantity);
            $standard_dinner_subtotal = $this->getMealSubtotal('1', 'dinner', $standard_dinner_quantity);
            $gluten_free_breakfast_subtotal = $this->getMealSubtotal('2', 'breakfast', $gluten_free_breakfast_quantity);
            $gluten_free_lunch_subtotal = $this->getMealSubtotal('2', 'lunch', $gluten_free_lunch_quantity);
            $gluten_free_dinner_subtotal = $this->getMealSubtotal('2', 'dinner', $gluten_free_dinner_quantity);
            $vegetarian_breakfast_subtotal = $this->getMealSubtotal('3', 'breakfast', $vegetarian_breakfast_quantity);
            $vegetarian_lunch_subtotal = $this->getMealSubtotal('3', 'lunch', $vegetarian_lunch_quantity);
            $vegetarian_dinner_subtotal = $this->getMealSubtotal('3', 'dinner', $vegetarian_dinner_quantity);
            $vegan_breakfast_subtotal = $this->getMealSubtotal('4', 'breakfast', $vegan_breakfast_quantity);
            $vegan_lunch_subtotal = $this->getMealSubtotal('4', 'lunch', $vegan_lunch_quantity);
            $vegan_dinner_subtotal = $this->getMealSubtotal('4', 'dinner', $vegan_dinner_quantity);
            $keto_breakfast_subtotal = $this->getMealSubtotal('5', 'breakfast', $keto_breakfast_quantity);
            $keto_lunch_subtotal = $this->getMealSubtotal('5', 'lunch', $keto_lunch_quantity);
            $keto_dinner_subtotal = $this->getMealSubtotal('5', 'dinner', $keto_dinner_quantity);
            $diabetic_breakfast_subtotal = $this->getMealSubtotal('6', 'breakfast', $diabetic_breakfast_quantity);
            $diabetic_lunch_subtotal = $this->getMealSubtotal('6', 'lunch', $diabetic_lunch_quantity);
            $diabetic_dinner_subtotal = $this->getMealSubtotal('6', 'dinner', $diabetic_dinner_quantity);
            $add_on_juice_subtotal = $this->getAddOnSubtotal(1, $add_on_juice_quantity);
            $add_on_fruit_subtotal = $this->getAddOnSubtotal(1, $add_on_fruit_quantity);
            $add_on_hummus_subtotal = $this->getAddOnSubtotal(1, $add_on_hummus_quantity);
            $pickup_or_delivery_subtotal = 0;
            if ($pickup_or_delivery == 'delivery') {
                $pickup_or_delivery_subtotal = 5;
            }
            $line_items = array(
                array("Standard Breakfast", $standard_breakfast_quantity, $standard_breakfast_subtotal),
                array("Standard Lunch", $standard_lunch_quantity, $standard_lunch_subtotal),
                array("Standard Dinner", $standard_dinner_quantity, $standard_dinner_subtotal),
                array("Gluten Free Breakfast", $gluten_free_breakfast_quantity, $gluten_free_breakfast_subtotal),
                array("Gluten Free Lunch", $gluten_free_lunch_quantity, $gluten_free_lunch_subtotal),
                array("Gluten Free Dinner", $gluten_free_dinner_quantity, $gluten_free_dinner_subtotal),
                array("Vegetarian Breakfast", $vegetarian_breakfast_quantity, $vegetarian_breakfast_subtotal),
                array("Vegetarian Lunch", $vegetarian_lunch_quantity, $vegetarian_lunch_subtotal),
                array("Vegetarian Dinner", $vegetarian_dinner_quantity, $vegetarian_dinner_subtotal),
                array("Vegan Breakfast", $vegan_breakfast_quantity, $vegan_breakfast_subtotal),
                array("Vegan Lunch", $vegan_lunch_quantity, $vegan_lunch_subtotal),
                array("Vegan Dinner", $vegan_dinner_quantity, $vegan_dinner_subtotal),
                array("Keto Breakfast", $keto_breakfast_quantity, $keto_breakfast_subtotal),
                array("Keto Lunch", $keto_lunch_quantity, $keto_lunch_subtotal),
                array("Keto Dinner", $keto_dinner_quantity, $keto_dinner_subtotal),
                array("Diabetic Breakfast", $diabetic_breakfast_quantity, $diabetic_breakfast_subtotal),
                array("Diabetic Lunch", $diabetic_lunch_quantity, $diabetic_lunch_subtotal),
                array("Diabetic Dinner", $diabetic_dinner_quantity, $diabetic_dinner_subtotal),
                array("Fresh Pressed Juice", $add_on_juice_quantity, $add_on_juice_subtotal),
                array("Seasonal Fruit Salad", $add_on_fruit_quantity, $add_on_fruit_subtotal),
                array("Hummus & Veggie Platter", $add_on_hummus_quantity, $add_on_hummus_subtotal),
                array("Delivery", 1, $pickup_or_delivery_subtotal)
            );
            $subtotal = $standard_breakfast_subtotal + $standard_lunch_subtotal + $standard_dinner_subtotal + $vegetarian_breakfast_subtotal + $vegetarian_lunch_subtotal +                            $vegetarian_dinner_subtotal + $vegan_breakfast_subtotal + $vegan_lunch_subtotal + $vegan_dinner_subtotal + $gluten_free_breakfast_subtotal + $gluten_free_lunch_subtotal +                $gluten_free_dinner_subtotal + $keto_breakfast_subtotal + $keto_lunch_subtotal + $keto_dinner_subtotal + $diabetic_breakfast_subtotal + $diabetic_lunch_subtotal +                        $diabetic_dinner_subtotal + $add_on_juice_subtotal + $add_on_fruit_subtotal + $add_on_hummus_subtotal + $pickup_or_delivery_subtotal;
            $tax = $this->calculateTax($subtotal);
            $total = $subtotal + $tax;
            $show_total_table = true;
            $template = $this->twig->load('order_now.twig');
            echo $template->render(['order_error_message' => $order_error_message, 'meal_plans' => $meal_plans, 'add_ons' => $add_ons, 'zip_codes' => $zip_codes, 'subtotal' => $subtotal, 'line_items' => $line_items, 'tax' => $tax, 'total' => $total, 'show_total_table' => $show_total_table,
                'standard_breakfast_quantity' => $standard_breakfast_quantity, 'standard_lunch_quantity' => $standard_lunch_quantity, 'standard_dinner_quantity' => $standard_dinner_quantity,
                'gluten_free_breakfast_quantity' => $gluten_free_breakfast_quantity, 'gluten_free_lunch_quantity' => $gluten_free_lunch_quantity, 'gluten_free_dinner_quantity' => $gluten_free_dinner_quantity,
                'vegetarian_breakfast_quantity' => $vegetarian_breakfast_quantity, 'vegetarian_lunch_quantity' => $vegetarian_lunch_quantity, 'vegetarian_dinner_quantity' => $vegetarian_dinner_quantity,
                'vegan_breakfast_quantity' => $vegan_breakfast_quantity, 'vegan_lunch_quantity' => $vegan_lunch_quantity, 'vegan_dinner_quantity' => $vegan_dinner_quantity,
                'keto_breakfast_quantity' => $keto_breakfast_quantity, 'keto_lunch_quantity' => $keto_lunch_quantity, 'keto_dinner_quantity' => $keto_dinner_quantity,
                'diabetic_breakfast_quantity' => $diabetic_breakfast_quantity, 'diabetic_lunch_quantity' => $diabetic_lunch_quantity, 'diabetic_dinner_quantity' => $diabetic_dinner_quantity]);
        }
    }

    function getMealSubtotal($plan_id, $meal, $quantity) {
        $meal_plan = $this->meal_plans_table->get_meal_plan($plan_id);
        $subtotal = doubleval($meal_plan[$meal . 'Price']) * doubleval($quantity);
        return $subtotal;
    }

    function getAddOnSubtotal($add_on_id, $quantity) {
        $add_on = $this->add_ons_table->get_add_on($add_on_id);
        $subtotal = doubleval($add_on['addOnPrice']) * doubleval($quantity);
        return $subtotal;
    }

    function calculateTax($subtotal) {
        $TAX_RATE = 0.0625;
        $tax = $subtotal * $TAX_RATE;
        return $tax;
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
     * Add session variables to twig upon login and signup
     */
    private function addToSessionGlobal($username, $first_name, $dietary_preference, $customer_id) {
        $_SESSION['is_valid_user'] = true;
        $_SESSION['username'] = $username;
        $_SESSION['first_name'] = $first_name;
        $_SESSION['dietary_preference'] = $dietary_preference;
        $_SESSION['customer_id'] = $customer_id;
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
