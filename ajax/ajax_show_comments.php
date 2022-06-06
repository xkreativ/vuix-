<?php 

    session_start();
    require_once '../vendor/db.php';

    // C какого поста будет осуществляться вывод 
    $dataAttr = $_POST['dataAttr'];
    
    // Получаем 6 постов, начиная с последней отображенной
    $comments = $db->prepare("SELECT comments.id, comments.id_post, comments.text, comments.added, comments.date,
    users.id_user, users.avatar, users.name, users.surname, users.login, :me AS me
    FROM `comments` JOIN `users` ON comments.added = users.id_user WHERE `id_post` = :id_post");
    $comments->execute([
    "id_post" => $dataAttr,
    "me" => $_SESSION["user"]["id_user"],
    ]);
    
    $comments = $comments->fetchAll();

    // Превращаем массив статей в json-строку для передачи через Ajax-запрос
    echo json_encode($comments);