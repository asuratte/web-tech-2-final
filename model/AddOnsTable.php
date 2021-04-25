<?php

class AddOnsTable {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    function get_add_ons() {
        $query = 'SELECT * FROM addOns
                  ORDER BY addOnID';
        $add_ons = $this->db->getDB()->prepare($query);
        $add_ons->execute();
        return $add_ons;
    }
    
    function get_add_on($add_on_id) {
        $query = 'SELECT * FROM addOns
              WHERE addOnID = :add_on_id';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':add_on_id', $add_on_id);
        $statement->execute();
        $add_on = $statement->fetch();
        $statement->closeCursor();
        return $add_on;
    }

}

?>