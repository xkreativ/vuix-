<?php

    require_once 'db.php';
    session_start();

    $text = $_POST['helptext'];
    
    $app_user = $db->prepare("INSERT INTO `help` (`id_user`, `text`, `created_at`, `answer`) 
    VALUES (:id_user, :text, :created_at, '')");
    $app_user->execute([
        "id_user" => $_SESSION["user"]["id_user"],
        "text" => $text,
        "created_at" => date("Y-m-d H:i:s"),
    ]); 

    header('Location: /help');