<?php
require_once("includes/database_conn.php");

if(isset($_POST['order_id'])){
    $order_id = intval($_POST['order_id']);

    // delete order items first (FK constraint)
    $conn->query("DELETE FROM Order_Item WHERE order_id=$order_id");

    // delete order
    if($conn->query("DELETE FROM `Order` WHERE order_id=$order_id")){
        echo "Order $order_id deleted successfully!";
    } else {
        echo "Error deleting order!";
    }
}
?>
