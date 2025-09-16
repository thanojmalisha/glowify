<?php

require_once("includes/database_conn.php");

function send_json($arr){
    header("Content-Type: application/json");
    echo json_encode($arr); exit;
}

if(isset($_GET['action'])){
    $action = $_GET['action'];

    if($action==="read"){
        $res = $conn->query("SELECT p.product_id, p.name, p.description, p.price, p.stock_quantity,
                             p.category_id, p.image_url, c.name AS category
                             FROM Product p LEFT JOIN Category c ON p.category_id=c.category_id
                             ORDER BY p.product_id DESC");
        $rows = [];
        while($r = $res->fetch_assoc()) $rows[] = $r;
        send_json(["success"=>true,"data"=>$rows]);
    }