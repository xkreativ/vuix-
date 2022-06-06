<?php

    session_start();
    require_once '../vendor/db.php';

        $user = $_POST['search-users'];


        $search = $db->prepare("SELECT * FROM `users` WHERE `login` LIKE '%$user%' OR `name` LIKE '%$user%' OR `surname` LIKE '%$user%' AND `id_user` NOT IN (:my_id) LIMIT 10");
        $search->execute([
            'my_id' => $_SESSION["user"]["id_user"],
        ]);
        $search = $search->fetchAll();
    

    echo json_encode($search);
?>