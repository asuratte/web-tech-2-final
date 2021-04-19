<?php

class Database {

    private $db;
    private $error_message;

    /**
     * Instantiates a new Database object that connects
     * to the database
     */
    public function __construct() {
        $dsn = 'mysql:host=localhost;dbname=madison_street_meal_prep';
        $username = 'madison_street_meal_prep';
        $password = 'Woodstock01';
        $this->error_message = '';
        try {
            $this->db = new PDO($dsn, $username, $password);
        } catch (PDOException $e) {
            $this->error_message = $e->getMessage();
        }
    }

    /**
     * Checks the connection to the database
     *
     * @return boolean - True if a connection has been established, false if not
     */
    public function isConnected() {
        return ($this->db != Null);
    }

    /**
     * Returns the error message
     * 
     * @return string - The error message
     */
    public function getErrorMessage() {
        return $this->error_message;
    }

    public function getDB() {
        return $this->db;
    }

}

?>