<?php 

    session_start();
    require_once '../vendor/db.php';


    $dataAttr = $_POST['dataAttr'];

    // Получаем 6 постов, начиная с последней отображенной
    $chat = $db->prepare("DELETE FROM `chat` WHERE chat_id = :chat_id");
    $chat->execute([
    "chat_id" => $dataAttr,
    ]);
    
    $chat = $chat->fetchAll();

    // Превращаем массив статей в json-строку для передачи через Ajax-запрос
    echo json_encode($chat);