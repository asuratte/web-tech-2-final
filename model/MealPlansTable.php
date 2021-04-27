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

    function get_meal_plan($plan_id) {
        $query = 'SELECT * FROM mealPlans
              WHERE planID = :plan_id';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':plan_id', $plan_id);
        $statement->execute();
        $meal_plan = $statement->fetch();
        $statement->closeCursor();
        return $meal_plan;
    }

    function get_current_meals() {
        $query = 'SELECT currentMeals.planID, currentMeals.breakfastName, currentMeals.breakfastDescription, currentMeals.breakfastImage, currentMeals.lunchName, currentMeals.lunchDescription, currentMeals.lunchImage, currentMeals.dinnerName, currentMeals.dinnerDescription, currentMeals.dinnerImage, mealPlans.planName
                FROM currentMeals
                INNER JOIN mealPlans ON currentMeals.planID=mealPlans.planID';
         $statement = $this->db->getDB()->prepare($query);
        $statement->execute();
        $current_meals = $statement->fetchAll();
        $statement->closeCursor();
        return $current_meals;
    }

}

?>