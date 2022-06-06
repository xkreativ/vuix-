<?php 

    session_start();
    require_once '../vendor/db.php';

    $dataAttr = $_POST['dataAttr'];

    if (isset($_POST["comment"])) { 

        $posts = $db->prepare("INSERT INTO `comments`(`id_post`, `text`, `added`, `date`) VALUES (:id_post, :text, :added , :date)");
        $posts->execute([
        "id_post" => $dataAttr,
        "text" => $_POST["comment"],
        "added" => $_SESSION["user"]["id_user"],
        "date" => date("Y-m-d H:i:s"),
        ]);
        $posts = $posts->fetchAll();

        $me = $db->prepare("SELECT users.id_user, users.name, users.login, users.surname, users.avatar, max(comments.id) as 'maxid' 
        FROM `users` JOIN `comments` ON comments.added = users.id_user WHERE users.id_user = :id_user
        ");
        $me->execute([
        "id_user" => $_SESSION["user"]["id_user"],
    ]);
        $me = $me->fetch(); 
        $me['comment'] = $_POST["comment"];
        $me['me'] = $_SESSION["user"]["id_user"];
        // Переводим массив в JSON
        echo json_encode($me); 
    }