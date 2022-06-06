<?php 

    session_start();
    require_once '../vendor/db.php';

    $section = $_GET['section'];
    $idUser = $_GET['id'];

    $id_user = $_SESSION["user"]["id_user"];

    if($idUser != null) {
        $id_user = $idUser;
    }

    if($section == 'subscribers') {
        $subscribers = $db->prepare("SELECT  users.id_user, users.name, users.login, users.surname, users.info, users.avatar, ucres.id_followed as yes
        FROM `users_connections` as uc1 JOIN `users` ON uc1.id_follower = users.id_user 
        
        LEFT JOIN (
            SELECT  uc2.id_followed
            FROM `users_connections` as uc2
            WHERE `id_follower` = :id_user
        ) `ucres` ON `ucres`.`id_followed` = users.`id_user`
        
        WHERE uc1.`id_followed` = :id_user");
        $subscribers->execute([
        "id_user" => $id_user,
        ]);
        $subscribers = $subscribers->fetchAll();

        echo json_encode($subscribers, JSON_UNESCAPED_UNICODE);
    } else if($section == 'subscriptions') {
        $subscriptions = $db->prepare("SELECT  users.id_user, users.name, users.login, users.surname, users.info, users.avatar
        FROM `users_connections` JOIN `users` ON users_connections.id_followed = users.id_user 
        WHERE `id_follower` = :id_user");
        $subscriptions->execute([
        "id_user" => $id_user,
        ]);
        $subscriptions = $subscriptions->fetchAll();

        echo json_encode($subscriptions, JSON_UNESCAPED_UNICODE);
    } 