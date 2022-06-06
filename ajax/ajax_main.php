<?php 

    session_start();
    require_once '../vendor/db.php';

    // C какого поста будет осуществляться вывод 
    $startFrom = $_POST['startFrom'];

    $prewweek = date("Y-m-d H:i:s", time()-60*60*24*30);
    $nowdate = date("Y-m-d H:i:s");
    // Получаем 6 постов, начиная с последней отображенной
    $friendPosts = $db->prepare("SELECT posts.id_post, posts.text_post, posts.photo, posts.created_at, users.id_user, users.login, users.name, users.surname, users.avatar
    FROM `posts` INNER JOIN `users_connections` ON posts.id_user = users_connections.id_followed INNER JOIN `users` ON users.id_user = posts.id_user
    WHERE users_connections.id_follower = :id_follower AND `created_at` BETWEEN '$prewweek' AND '$nowdate' ORDER BY `created_at` DESC LIMIT {$startFrom} , 5");
    $friendPosts->execute([
    "id_follower" => $_SESSION['user']['id_user'],
    ]);
    $friendPosts = $friendPosts->fetchAll();

    // Превращаем массив статей в json-строку для передачи через Ajax-запрос
    echo json_encode($friendPosts);