<?php 

    session_start();
    require_once '../vendor/db.php';

    // C какого поста будет осуществляться вывод 
    $startFrom = $_POST['startFrom'];
    $page = $_POST['page'];

    // Получаем 6 постов, начиная с последней отображенной
    $info = $db->prepare("SELECT `id_user`, `login` FROM `users` WHERE `login` = :login");
        $info->execute([
        "login" => $page,
    ]);
        $info = $info->fetch(); 
    
        $userPosts = $db->prepare("SELECT * FROM `posts` WHERE `id_user` = :id_user ORDER BY `created_at` DESC LIMIT ${startFrom} , 6");
    $userPosts->execute([
    "id_user" => $info['id_user'],
    ]);
    $userPosts = $userPosts->fetchAll();

    // Превращаем массив статей в json-строку для передачи через Ajax-запрос
    echo json_encode($userPosts);