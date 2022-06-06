<?php 

    session_start();
    require_once '../vendor/db.php';


    $dataAttr = $_POST['dataDelete'];

    // Получаем 6 постов, начиная с последней отображенной
    $comments = $db->prepare("DELETE FROM `help` WHERE `id_help` = :id_help");
    $comments->execute([
    "id_help" => $dataAttr,
    ]);
    
    $comments = $comments->fetchAll();

    // Превращаем массив статей в json-строку для передачи через Ajax-запрос
    echo json_encode($comments);