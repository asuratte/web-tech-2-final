<?php

class OrdersTable {

    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    function get_orders() {
        $query = 'SELECT * FROM orders
                  ORDER BY orderID';
        $orders = $this->db->getDB()->prepare($query);
        $orders->execute();
        return $orders;
    }

    function get_order_line_items($order_id) {
        $query = 'SELECT * FROM lineItems
              WHERE order_id = :orderID';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':orderID', $order_id);
        $statement->execute();
        $order_line_items = $statement->fetch();
        return $order_line_items;
    }

}

?>