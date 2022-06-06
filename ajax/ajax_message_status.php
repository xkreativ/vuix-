<?php 

    session_start();
    require_once '../vendor/db.php';


    $dataAttr = $_POST['dataAttr'];

    $status = $db->prepare("DELETE FROM `message_status` WHERE `chat_id` = :chat_id");
    $status->execute([
    "chat_id" => $dataAttr,
    ]);
    
    $status = $status->fetchAll();
    echo json_encode($status);