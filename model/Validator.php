<?php

class Validator {

    private $db;
    private $customers_table;

    /**
     * Instantiates a new validator
     */
    public function __construct($db, $customers_table) {
        $this->db = $db;
        $this->customers_table = $customers_table;
    }

    public function checkTextField($value,
            $required = true, $min_chars = 1, $max_chars = 255) {
        $error_message = '';
        if (!$required && empty($value)) {
            return;
        }
        if ($required && empty($value)) {
            $error_message = 'Required';
        } else if (strlen($value) < $min_chars) {
            $error_message = 'Must have at least ' . $min_chars . ' character(s).';
        } else if (strlen($value) > $max_chars) {
            $error_message = $max_chars . ' character limit has been exceeded';
        }
        return $error_message;
    }

    public function checkValidationPattern($value, $pattern, $error_message,
            $required = true) {
        if (!$required && empty($value)) {
            $error_message = '';
            return;
        }
        $match = preg_match($pattern, $value);
        if ($match === false) {
            $error_message = 'Error testing field.';
        } else if ($match != 1) {
            $error_message = $error_message;
        } else {
            $error_message = '';
        }
        return $error_message;
    }

    public function checkUsername($value, $required = true) {
        $error_message = $this->checkTextField($value, $required, 9);
        if (!empty($error_message)) {
            return $error_message;
        } else if ($this->customers_table->checkUsernameExists($value) == true) {
            $error_message = 'The username you have entered is already taken. Please choose a new one.';
            return $error_message;
        }
    }

    public function checkUpdateUsername($value, $required = true) {
        $error_message = $this->checkTextField($value, $required, 9);
        if (!empty($error_message)) {
            return $error_message;
        } else if ($this->customers_table->checkUsernameExists($value) == true && $value != $_SESSION['username']) {
            $error_message = 'The username you have entered is already taken. Please choose a new one.';
            return $error_message;
        }
    }

    public function checkPhone($value, $required = true) {
        $error_message = $this->checkTextField($value, $required);
        $pattern = '/^\([[:digit:]]{3}\)\s[[:digit:]]{3}-[[:digit:]]{4}$/';
        $pattern_match_error_message = 'Use (999) 999-9999 format';
        $pattern_match = $this->checkValidationPattern($value, $pattern, $pattern_match_error_message, $required);
        if (!empty($error_message)) {
            return $error_message;
        } else if (!empty($pattern_match)) {
            return $pattern_match;
        }
    }

    public function checkZipCode($value, $required, $min, $max) {
        $error_message = $this->checkTextField($value, $required, $min, $max);
        $pattern = '/^[0-9]{5}(?:-[0-9]{4})?$/';
        $pattern_match_error_message = 'Use 99999 or 99999-9999 format';
        $pattern_match = $this->checkValidationPattern($value, $pattern, $pattern_match_error_message, $required);
        if (!empty($error_message)) {
            return $error_message;
        } else if (!empty($pattern_match)) {
            return $pattern_match;
        }
    }

    public function checkEmail($value, $required = true) {
        $error_message = $this->checkTextField($value, $required, 1, 320);
        if (!empty($error_message)) {
            return $error_message;
        }

        // Split email address on @ sign and check parts
        $parts = explode('@', $value);
        if (count($parts) < 2) {
            $error_message = '"@" sign required.';
            return $error_message;
        }
        if (count($parts) > 2) {
            $error_message = 'Only one "@" sign allowed.';
            return $error_message;
        }
        $local = $parts[0];
        $domain = $parts[1];

        // Check lengths of local and domain parts
        if (strlen($local) > 64) {
            $error_message = 'Username part too long.';
            return $error_message;
        }
        if (strlen($domain) > 255) {
            $error_message = 'Domain name part too long.';
            return $error_message;
        }

        // Patterns for address formatted local part
        $atom = '[[:alnum:]_!#$%&\'*+\/=?^`{|}~-]+';
        $dotatom = '(\.' . $atom . ')*';
        $address = '(^' . $atom . $dotatom . '$)';

        // Patterns for quoted text formatted local part
        $char = '([^\\\\"])';
        $esc = '(\\\\[\\\\"])';
        $text = '(' . $char . '|' . $esc . ')+';
        $quoted = '(^"' . $text . '"$)';

        // Combined pattern for testing local part
        $localPattern = '/' . $address . '|' . $quoted . '/';

        // Call the pattern method and exit if it yields an error
        $pattern_match_host = $this->checkValidationPattern($local, $localPattern,
                'Invalid username part.');
        if (!empty($pattern_match_host)) {
            return $pattern_match_host;
        }

        // Patterns for domain part
        $hostname = '([[:alnum:]]([-[:alnum:]]{0,62}[[:alnum:]])?)';
        $hostnames = '(' . $hostname . '(\.' . $hostname . ')*)';
        $top = '\.[[:alnum:]]{2,6}';
        $domainPattern = '/^' . $hostnames . $top . '$/';

        // Call the pattern method
        $pattern_match_domain = $this->checkValidationPattern($domain, $domainPattern,
                'Invalid domain name part.');

        if (!empty($pattern_match_domain)) {
            return $pattern_match_domain;
        }
    }

    public function checkPassword($value) {
        $error_message = $this->checkTextField($value, $required = true);
        if (!empty($error_message)) {
            return $error_message;
        }
        $pattern = '/^(?=.*[[:digit:]])(?=.*[[:upper:]])(?=.*[[:lower:]])[[:print:]]{10,}?/';
        $pattern_match_error_message = 'Password requires at least 10 characters including a number, a lowercase, and an uppercase letter.';
        $pattern_match = $this->checkValidationPattern($value, $pattern, $pattern_match_error_message, $required = true);
        if (!empty($pattern_match)) {
            return $pattern_match;
        }
    }

    public function checkQuantityField($value) {
        $error_message = $this->checkTextField($value, $required = false);
        if (!empty($error_message)) {
            return $error_message;
        }
        $pattern = '/^([0-1]?[0-9]|20)$/';
        $pattern_match_error_message = 'Quantity must be between 0-20.';
        $pattern_match = $this->checkValidationPattern($value, $pattern, $pattern_match_error_message, $required = false);
        if (!empty($pattern_match)) {
            return $pattern_match;
        }
    }

    public function checkPasswordsMatch($confirm_password, $password) {
        $error_message = $this->checkTextField($confirm_password, $required = true);
        if (!empty($error_message)) {
            return $error_message;
        }
        if ($confirm_password === $password) {
            $error_message = '';
            return $error_message;
        } else {
            $error_message = "Passwords don't match.";
            return $error_message;
        }
    }

}

?>