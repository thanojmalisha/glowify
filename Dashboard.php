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
    if($action==="create" || $action==="update"){
        $id = $_POST['product_id'] ?? null;
        $name = $_POST['name'] ?? "";
        $desc = $_POST['description'] ?? "";
        $price = floatval($_POST['price'] ?? 0);
        $stock = intval($_POST['stock_quantity'] ?? 0);
        $cat = $_POST['category_id'] ?? null;
        $img = $_POST['image_url'] ?? "";

        if($action==="create"){
            $stmt = $conn->prepare("INSERT INTO Product (name, description, price, stock_quantity, category_id, image_url) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("ssdiis",$name,$desc,$price,$stock,$cat,$img);
            $stmt->execute();
            send_json(["success"=>true,"id"=>$conn->insert_id]);
        } else {
            $stmt = $conn->prepare("UPDATE Product SET name=?, description=?, price=?, stock_quantity=?, category_id=?, image_url=? WHERE product_id=?");
            $stmt->bind_param("ssdiisi",$name,$desc,$price,$stock,$cat,$img,$id);
            $stmt->execute();
            send_json(["success"=>true]);
        }
    }

    if($action==="delete"){
        $id = $_POST['product_id'] ?? 0;
        $stmt = $conn->prepare("DELETE FROM Product WHERE product_id=?");
        $stmt->bind_param("i",$id);
        $stmt->execute();
        send_json(["success"=>true]);
    }
}
?>