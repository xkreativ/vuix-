<?php 

    session_start();
    require_once '../vendor/db.php';

    $dialogsId = $_GET['dialogsId'];
    $id_user = $_SESSION["user"]["id_user"];

    $subscribers = $db->prepare("SELECT * FROM `messages` WHERE chat_id = :dialogsId");
    $subscribers->execute([
        "dialogsId" => $dialogsId
    ]);
    $subscribers = $subscribers->fetchAll();

    echo json_encode($subscribers, JSON_UNESCAPED_UNICODE);
    