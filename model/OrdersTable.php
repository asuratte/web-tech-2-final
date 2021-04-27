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
    
    function get_orders_and_line_items_ascending($customer_id) {
        $query = 'SELECT orders.orderID, orders.orderDate, orders.orderTotal, lineItems.itemName, lineItems.itemQuantity, lineItems.itemTotal
                FROM orders
                INNER JOIN lineItems ON orders.orderID=lineItems.orderID
                WHERE customerID = :customer_id
                ORDER BY orders.orderDate asc';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':customer_id', $customer_id);
        $statement->execute();
        $orders_and_line_items = $statement->fetchAll();
        $statement->closeCursor();
        return $orders_and_line_items;
    }
    
    function add_order($order_date, $order_total, $customer_id) {
        $query = 'INSERT INTO orders (orderDate, orderTotal, customerID)
              VALUES (:order_date, :order_total, :customer_id)';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':order_date', $order_date);
        $statement->bindValue(':order_total', $order_total);
        $statement->bindValue(':customer_id', $customer_id);
        $statement->execute();
        $statement->closeCursor();
    }
    
    function get_customer_last_order_id($customer_id) {
        $query = 'SELECT orderID 
                FROM orders 
                WHERE customerID = :customer_id
                ORDER BY orders.orderDate desc';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':customer_id', $customer_id);
        $statement->execute();
        $order_id= $statement->fetch();
        $statement->closeCursor();
        $order_id = $order_id[0];
        return $order_id;
    }
    
    function add_line_item($order_id, $item_quantity, $item_name, $item_total) {
        $query = ' INSERT INTO lineItems (orderID, itemQuantity, itemName, itemTotal)
                VALUES (:order_id, :item_quantity, :item_name, :item_total)';
        $statement = $this->db->getDB()->prepare($query);
        $statement->bindValue(':order_id', $order_id);
        $statement->bindValue(':item_quantity', $item_quantity);
        $statement->bindValue(':item_name', $item_name);
        $statement->bindValue(':item_total', $item_total);
        $statement->execute();
        $statement->closeCursor();
    }
    
}

?>