<?php

class FaqTable {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    function get_faqs() {
        $query = 'SELECT * FROM frequentlyAskedQuestions
                  ORDER BY faqID';
        $faqs = $this->db->getDB()->prepare($query);
        $faqs->execute();
        return $faqs;
    }

}

?>