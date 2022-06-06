<?php

    session_start();
    require_once '../vendor/db.php';

    $dataAttr = $_POST['dataAttr'];

    $showonclick = $_POST['showonclick'];

    $likedCheck = $db->prepare("SELECT * FROM `likes_post` WHERE `id_post` = :id_post AND `id_user` = :id_user");
        $likedCheck->execute([
            "id_post" => $dataAttr,
            "id_user" => $_SESSION["user"]["id_user"],
        ]);
        $likedCheck = $likedCheck->fetch();

    if($showonclick) {

        /*------------------------------- Добавляем если человек не поставил лайк ------------------------- */
        if( !$likedCheck ) {
            $app_user = $db->prepare("INSERT INTO `likes_post` (`id_user`, `id_post`, `like_at`) VALUES (:id_user, :id_post, :like_at)");
            $app_user->execute([
                "id_user" => $_SESSION["user"]["id_user"],
                "id_post" => $dataAttr,
                "like_at" => date("Y-m-d H:i:s"),
            ]); 
        /*------------------------------- Удаляем если человек поставил лайк ------------------------- */
        } else {
            $app_user = $db->prepare("DELETE FROM `likes_post` WHERE `id_post` = :id_post AND `id_user` = :id_user");
            $app_user->execute([
                "id_post" => $dataAttr,
                "id_user" => $_SESSION["user"]["id_user"],
            ]);
        }
    }

    echo json_encode($likedCheck);