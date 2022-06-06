<?php 

    session_start();
    require_once '../vendor/db.php';


    $dataAttr = $_POST['dataPost'];

    // Получаем 6 постов, начиная с последней отображенной
    $comments = $db->prepare("DELETE FROM `comments` WHERE `id` = :id_post");
    $comments->execute([
    "id_post" => $dataAttr,
    ]);
    
    $comments = $comments->fetchAll();

    // Превращаем массив статей в json-строку для передачи через Ajax-запрос
    echo json_encode($comments);