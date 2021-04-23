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

}

?>