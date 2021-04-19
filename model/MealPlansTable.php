<?php

class MealPlansTable {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    function get_meal_plans() {
        $query = 'SELECT * FROM mealPlans
                  ORDER BY planID';
        $meal_plans = $this->db->getDB()->prepare($query);
        $meal_plans->execute();
        return $meal_plans;
    }

}

?>