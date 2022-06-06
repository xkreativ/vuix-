<?php 

    session_start();
    require_once '../vendor/db.php';

    // C какого поста будет осуществляться вывод 
    $startFrom = $_POST['startFrom'];

    // Получаем 6 постов, начиная с последней отображенной
    $posts = $db->prepare("SELECT * FROM `posts` WHERE  `id_user` = :id_user ORDER BY `created_at` DESC LIMIT {$startFrom} , 6 ");
    $posts->execute([
    "id_user" => $_SESSION["user"]["id_user"],
    ]);
    $posts = $posts->fetchAll();

    // Превращаем массив статей в json-строку для передачи через Ajax-запрос
    echo json_encode($posts);