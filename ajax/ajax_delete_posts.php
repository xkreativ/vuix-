<?php 

    session_start();
    require_once '../vendor/db.php';


    $dataAttr = $_POST['dataDelete'];

    // Получаем 6 постов, начиная с последней отображенной
    $comments = $db->prepare("DELETE FROM `posts` WHERE `id_post` = :id_post");
    $comments->execute([
    "id_post" => $dataAttr,
    ]);
    
    $comments = $comments->fetchAll();

    // Превращаем массив статей в json-строку для передачи через Ajax-запрос
    echo json_encode($comments);