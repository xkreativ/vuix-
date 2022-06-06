<?php 

    session_start();
    require_once '../vendor/db.php';

    // C какого поста будет осуществляться вывод 
    $dataComment = $_POST['dataComment'];
    $dataPost = $_POST['dataPost'];

    
    // Получаем 6 постов, начиная с последней отображенной
    $comments = $db->prepare("SELECT comments.*, users.id_user, users.name, users.surname, users.login, users.avatar FROM `comments`
    JOIN users ON users.id_user = comments.added
    WHERE id_post = :id_post AND id != :id");
    $comments->execute([
    "id_post" => $dataPost,
    "id" => $dataComment
    ]);
    
    $comments = $comments->fetchAll();

    // Превращаем массив статей в json-строку для передачи через Ajax-запрос
    echo json_encode($comments);