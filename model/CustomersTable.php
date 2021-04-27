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

    function get_customer_by_username($username) {
        $query = 'SELECT * FROM customers
              WHERE username = :username';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $customer = $statement->fetch();
        $statement->closeCursor();
        return $customer;
    }

    function add_customer($first_name, $last_name,
            $street_address, $city, $state, $zip_code, $phone, $email,
            $dietary_preference, $username, $password) {
        $query = 'INSERT INTO customers (firstName, lastName, streetAddress, city, state, zipCode, phoneNumber, email, dietaryPreference, username, password)
              VALUES (:firstName, :lastName, :streetAddress, :city, :state, :zipCode, :phoneNumber, :email, :dietaryPreference, :username, :password)';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':firstName', $first_name);
        $statement->bindValue(':lastName', $last_name);
        $statement->bindValue(':streetAddress', $street_address);
        $statement->bindValue(':city', $city);
        $statement->bindValue(':state', $state);
        $statement->bindValue(':zipCode', $zip_code);
        $statement->bindValue(':phoneNumber', $phone);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':dietaryPreference', $dietary_preference);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':password', $password);
        $statement->execute();
        $statement->closeCursor();
    }

    function update_customer($first_name, $last_name,
            $street_address, $city, $state, $zip_code, $phone, $email,
            $dietary_preference, $username, $password, $customer_id) {
        $query = 'UPDATE customers
              SET firstName = :first_name,
                  lastName = :last_name,
                  streetAddress = :street_address,
                  city = :city,
                  state = :state,
                  zipCode = :zip_code,
                  phoneNumber = :phone,
                  email = :email,
                  dietaryPreference = :dietary_preference,
                  username = :username,
                  password = :password
              WHERE customerID = :customer_id';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':first_name', $first_name);
        $statement->bindValue(':last_name', $last_name);
        $statement->bindValue(':street_address', $street_address);
        $statement->bindValue(':city', $city);
        $statement->bindValue(':state', $state);
        $statement->bindValue(':zip_code', $zip_code);
        $statement->bindValue(':phone', $phone);
        $statement->bindValue(':email', $email);
        $statement->bindValue(':dietary_preference', $dietary_preference);
        $statement->bindValue(':username', $username);
        $statement->bindValue(':password', $password);
        $statement->bindValue(':customer_id', $customer_id);
        $statement->execute();
        $statement->closeCursor();
    }

    public function checkUsernameExists($username) {
        $query = 'SELECT * FROM customers
              WHERE username = :username';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':username', $username);
        $statement->execute();
        $row = $statement->fetch();
        $statement->closeCursor();
        if (!$row) {
            return false;
        } else {
            return true;
        }
    }

}

?>