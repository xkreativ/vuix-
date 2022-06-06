<?php 

    session_start();
    require_once '../vendor/db.php';

    // C какого поста будет осуществляться вывод 
    $dataAttr = $_POST['dataAttr'];
    
    // Получаем 6 постов, начиная с последней отображенной
    $users = $db->prepare("SELECT * FROM users 
    LEFT JOIN (
        SELECT users_connections.id_followed, COUNT(`id_follower`) as countfld FROM `users_connections` GROUP BY id_followed
    ) `cf` ON `cf`.`id_followed` = `users`.`id_user`
    WHERE id_user = :id_user");
    $users->execute([
    "id_user" => $dataAttr,

    ]);
    
    $users = $users->fetchAll();

    // Превращаем массив статей в json-строку для передачи через Ajax-запрос
    echo json_encode($users);