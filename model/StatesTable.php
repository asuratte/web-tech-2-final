<?php

class StatesTable {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    function get_states() {
        $query = 'SELECT * FROM states
                  ORDER BY abbreviation';
        $states = $this->db->getDB()->prepare($query);
        $states->execute();
        return $states;
    }

}

?>