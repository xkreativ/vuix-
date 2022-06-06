<?php 

    session_start();
    require_once '../../vendor/db.php';

    $dataAttr = $_POST['dataAttr'];

    $mychat = $db->prepare("SELECT p1.chat_id FROM party p1 
    JOIN party p2 ON p1.chat_id=p2.chat_id AND p1.id_user != p2.id_user
    WHERE p2.id_user = :id_user AND p1.id_user = :my_id
    ");
    $mychat->execute([
        "my_id" => $_SESSION["user"]["id_user"],
        "id_user" => $dataAttr,
    ]);
    $mychat = $mychat->fetchAll();

    if(empty($mychat)) {
        //СОЗДАЕМ ЧАТ
        $chat = $db->prepare("INSERT INTO `chat`(`date_create`) 
        VALUES (:date_create);
        SELECT LAST_INSERT_ID()");
        $chat->execute([
            "date_create" => date("Y-m-d H:i:s"),
        ]);
        $chat = $chat->fetchAll();
        //ПОЛУЧАЕМ ПОСЛЕДНЮЮ ЗАПИСЬ
        $last = $db->prepare("SELECT LAST_INSERT_ID()");
        $last->execute([
        ]);
        $last = $last->fetchAll();
        //ДОБАВЛЯЕМ УЧАСТНИКОВ ЧАТА -- СЕБЯ
        $party = $db->prepare("INSERT INTO `party`(`chat_id`, `id_user`, `date_create`) 
        VALUES (:chat_id,:id_user,:date_create)");
        $party->execute([
            "chat_id" => $last[0][0],
            "id_user" => $_SESSION["user"]["id_user"],
            "date_create" => date("Y-m-d H:i:s"),
        ]);
        //ДОБАВЛЯЕМ УЧАСТНИКОВ ЧАТА -- ДРУГОЙ
        $user = $db->prepare("INSERT INTO `party`(`chat_id`, `id_user`, `date_create`) 
        VALUES (:chat_id,:id_user,:date_create)");
        $user->execute([
            "chat_id" => $last[0][0],
            "id_user" => $dataAttr,
            "date_create" => date("Y-m-d H:i:s"),
        ]);


        $mychat = $db->prepare("SELECT p1.chat_id FROM party p1 
        JOIN party p2 ON p1.chat_id=p2.chat_id AND p1.id_user != p2.id_user
        WHERE p2.id_user = :id_user AND p1.id_user = :my_id
        ");
        $mychat->execute([
            "my_id" => $_SESSION["user"]["id_user"],
            "id_user" => $dataAttr,
        ]);
        $mychat = $mychat->fetchAll();
    }



    // Превращаем массив статей в json-строку для передачи через Ajax-запрос
    echo json_encode($mychat);