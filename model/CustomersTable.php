<?php

class CustomersTable {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    function get_customers() {
        $query = 'SELECT * FROM customers
                  ORDER BY customerID';
        $customers = $this->db->getDB()->prepare($query);
        $customers->execute();
        return $customers;
    }
    
    function add_customer($first_name, $last_name,
        $street_address, $city, $state, $zip_code, $phone, $email, 
            $dietary_preference, $username, $password) {
            $query = 'INSERT INTO customers
              VALUES (:$first_name, :last_name, :street_address, :city, :state, :zip_code, :phone, :email, :dietary_preference, :username, :password)';
            $statement = $this->db->getDB()->prepare($query);
            $statement->bindValue(':firstName', $first_name);
            $statement->bindValue(':lastName', $last_name);
            $statement->bindValue(':streetAddress', $street_address);
            $statement->bindValue(':city', $city);
            $statement->bindValue(':state', $state);
            $statement->bindValue(':zipCode', $zip_code);
            $statement->bindValue(':phone', $phone);
            $statement->bindValue(':email', $email);
            $statement->bindValue(':dietaryPreference', $dietary_preference);
            $statement->bindValue(':username', $username);
            $statement->bindValue(':password', $password);
            $statement->execute();
            $statement->closeCursor();
    }

}

?>