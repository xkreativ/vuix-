<?php 

    session_start();
    require_once '../vendor/db.php';


    $dataAttr = $_POST['dataDelete'];

    // Получаем 6 постов, начиная с последней отображенной
    $comments = $db->prepare("DELETE FROM `users` WHERE `id_user` = :id_user");
    $comments->execute([
    "id_user" => $dataAttr,
    ]);
    
    $comments = $comments->fetchAll();

    // Превращаем массив статей в json-строку для передачи через Ajax-запрос
    echo json_encode($comments);