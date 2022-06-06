<?php 

    session_start();
    require_once '../vendor/db.php';


    $dataAttr = $_POST['dataAttr'];

    // Получаем 6 постов, начиная с последней отображенной
    $message = $db->prepare("DELETE FROM `messages` WHERE message_id = :message_id");
    $message->execute([
    "message_id" => $dataAttr,
    ]);
    
    $message = $message->fetchAll();

    // Превращаем массив статей в json-строку для передачи через Ajax-запрос
    echo json_encode($message);