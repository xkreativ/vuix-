<?php 

    session_start();
    require_once '../vendor/db.php';

    // C какого поста будет осуществляться вывод 
    $dataAttr = $_POST['dataAttr'];
    
    // Получаем 6 постов, начиная с последней отображенной
    $comments = $db->prepare("SELECT posts.`text_post`, posts.`created_at`, COUNT(likes_post.id) as count FROM `posts`
    LEFT JOIN likes_post ON likes_post.id_post = posts.id_post
    WHERE posts.id_post = :id_post");
    $comments->execute([
    "id_post" => $dataAttr,
    ]);
    
    $comments = $comments->fetchAll();

    // Превращаем массив статей в json-строку для передачи через Ajax-запрос
    echo json_encode($comments);