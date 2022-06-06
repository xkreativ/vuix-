<?php 

    session_start();
    require_once '../vendor/db.php';

    $dataAttr = $_POST['dataAttr'];

    if (isset($_POST["message"])) { 

        $messages = $db->prepare("INSERT INTO `messages`(`chat_id`, `id_user`, `content`, `date_create`) 
        VALUES (:chat_id, :id_user, :content, :date_create)");
        $messages->execute([
        "chat_id" => $dataAttr,
        "id_user" => $_SESSION["user"]["id_user"],
        "content" => $_POST["message"],
        "date_create" => date("Y-m-d H:i:s"),
        ]);
        $messages = $messages->fetchAll();

        $me = $db->prepare("SELECT * FROM messages WHERE message_id = (SELECT * FROM (SELECT MAX(message_id) FROM (SELECT * FROM messages as m1 WHERE id_user = :id_user) as m2) as m3)");
        $me->execute([
        "id_user" => $_SESSION["user"]["id_user"],
        ]);
        $me = $me->fetch(); 

        $status = $db->prepare("INSERT INTO `message_status`(`message_id`, `chat_id`, `id_user`, `is_read`)
         VALUES (:message_id, :chat_id, :id_user, :is_read)");
        $status->execute([
        "message_id" => $me['message_id'],
        "chat_id" => $me['chat_id'],
        "id_user" => $_SESSION["user"]["id_user"],
        "is_read" => 1, 
        ]);

        $me['message'] = $_POST["message"];
        $me['me'] = $_SESSION["user"]["id_user"];
        // Переводим массив в JSON
        echo json_encode($me); 
    }