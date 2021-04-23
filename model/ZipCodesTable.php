<?php

class ZipCodesTable {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    function get_zip_codes() {
        $query = 'SELECT * FROM deliveryZipCodes
                  ORDER BY zipCode';
        $zip_codes = $this->db->getDB()->prepare($query);
        $zip_codes->execute();
        return $zip_codes;
    }

}

?>