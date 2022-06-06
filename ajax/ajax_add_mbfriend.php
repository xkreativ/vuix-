<?php 

    session_start();
    require_once '../vendor/db.php';

    $id_followed = $_GET["dataId"];
    $id_follower = $_SESSION["user"]["id_user"];
    $login = $_GET["dataLogin"];

    /*------------------------------- Проверка подписан ли человек ------------------------- */
    $followCheck = $db->prepare("SELECT * FROM `users_connections` WHERE `id_follower` = :id_follower AND `id_followed` = :id_followed");
    $followCheck->execute([
    "id_follower" => $id_follower,
    "id_followed" => $id_followed,
    ]);
    $followCheck = $followCheck->fetch();

    if($id_follower) {
        if($id_followed) {
            
            /*------------------------------- Добавляем если человек подписан ------------------------- */
            if( !$followCheck ) {
                $app_user = $db->prepare("INSERT INTO `users_connections` (`id_follower`, `id_followed`) VALUES (:id_follower, :id_followed)");
                $app_user->execute([
                    "id_follower" => $id_follower,
                    "id_followed" => $id_followed,
                ]); 
            /*------------------------------- Удаляем если человек подписан ------------------------- */
            } else {
                $app_user = $db->prepare("DELETE FROM `users_connections` WHERE `id_follower` = :id_follower AND `id_followed` = :id_followed");
                $app_user->execute([
                    "id_follower" => $id_follower,
                    "id_followed" => $id_followed,
                ]); 
            }
        }
    }

    echo json_encode($followCheck);