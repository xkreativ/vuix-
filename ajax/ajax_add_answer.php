<?php 

    session_start();
    require_once '../vendor/db.php';


    $dataAttr = $_POST['dataPost'];
    $textAnswer = $_POST['textAnswer'];
    

    // Получаем 6 постов, начиная с последней отображенной
    $comments = $db->prepare("UPDATE `help` SET `answer`=:textAnswer WHERE id_help= :id_help");
    $comments->execute([
    "id_help" => $dataAttr,
    "textAnswer" => $textAnswer,
    ]);
    
    $comments = $comments->fetchAll();

    // Превращаем массив статей в json-строку для передачи через Ajax-запрос
    echo json_encode($comments);