<?php

class OrdersTable {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    function get_orders_and_line_items($customer_id) {
        $query = 'SELECT orders.orderID, orders.orderDate, orders.orderTotal, lineItems.itemName, lineItems.itemQuantity, lineItems.itemTotal
                FROM orders
                INNER JOIN lineItems ON orders.orderID=lineItems.orderID
                WHERE customerID = :customer_id
                ORDER BY orders.orderDate desc';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':customer_id', $customer_id);
        $statement->execute();
        $orders_and_line_items = $statement->fetchAll();
        $statement->closeCursor();
        return $orders_and_line_items;
    }

}

?>