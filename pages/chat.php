<?php

    session_start();
    if(isset($_SESSION["auth"]) && $_SESSION["auth"] !== true) {
        header('Location: /');
    }
    if(!isset($_SESSION["auth"])) {
        header('Location: /');
    }

    require_once 'vendor/db.php';
    
    $dialogs = $db->prepare("SELECT DISTINCT p1.*, users.name, users.surname, users.avatar, ms1.count_status, ms1.is_read  FROM party p1 
    JOIN users ON p1.id_user = users.id_user 
    JOIN party p2 ON p1.chat_id=p2.chat_id AND p1.id_user != p2.id_user
    LEFT JOIN (
	SELECT `chat_id`, COUNT(`message_status`.`chat_id`) `count_status`, `is_read`
  	FROM `message_status`
        WHERE message_status.id_user != :id_user
  	GROUP BY `chat_id`, `is_read`
) `ms1` ON `p1`.`chat_id` = `ms1`.`chat_id`
        WHERE p2.id_user = :id_user
        ORDER BY ms1.count_status DESC");
    $dialogs->execute([
        "id_user" => $_SESSION["user"]["id_user"],
    ]);
    $dialogs = $dialogs->fetchAll();

    $myChat = $db->prepare("SELECT DISTINCT p1.*, users.name, users.surname, users.avatar, ms1.count_status, ms1.is_read  FROM party p1 
    JOIN users ON p1.id_user = users.id_user 
    JOIN party p2 ON p1.chat_id=p2.chat_id AND p1.id_user != p2.id_user
    LEFT JOIN (
	SELECT `chat_id`, COUNT(`message_status`.`chat_id`) `count_status`, `is_read`
  	FROM `message_status`
        WHERE message_status.id_user != :id_user
  	GROUP BY `chat_id`, `is_read`
) `ms1` ON `p1`.`chat_id` = `ms1`.`chat_id`
        WHERE p2.id_user = :id_user
        ORDER BY ms1.count_status DESC");
    $myChat->execute([
    "id_user" => $_SESSION["user"]["id_user"],
    ]);
    $myChat = $myChat->fetchAll();
    $arr = [];
    foreach ($myChat as $elem) {
        array_push($arr, $elem["chat_id"]);
    }
    if($arr != []) {
        $in  = str_repeat('?, ', count($arr) - 1) . '?';

    $q = "SELECT messages.*
    FROM (
        SELECT MAX(message_id) AS 'maxid' FROM `messages`
        WHERE chat_id IN ($in)
        GROUP BY chat_id
    ) AS a
    INNER JOIN messages ON a.maxid = message_id
    ORDER BY message_id DESC";

    $lastMessages = $db->prepare($q);
    $lastMessages->execute($arr); 
    $lastMessages = $lastMessages->fetchAll();
    }


    // $messages = $db->prepare("SELECT * FROM `messages` WHERE chat_id = :chat_id");
    // $messages->execute([
    //     "chat_id" => 1,
    // ]);
    // $messages = $messages->fetchAll();

?>



<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@900&display=swap" rel="stylesheet">
    <title>Сообщения</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <div class="wrapper">

    <div class="navbar">
    <aside class="navigation <?= $_COOKIE["nav-toggle"] == 1 ? 'active' : "" ?>">
    <div class="logo">VUIX</div>
    <ul>
        <li class="list">
                <a href="/main">
                    <span class="icon">
                        <span class="iconify" data-icon="clarity:home-line"></span>
                    </span>    
                    <span class="title">Главная</span>
                </a>
            </li>
            <li class="list">
                <a href="/profile">
                    <span class="icon">
                        <span class="iconify" data-icon="iconoir:profile-circled"></span>
                    </span>    
                    <span class="title">Профиль</span>
                </a>
            </li>
            <li class="list">
                <a href="/friends">
                    <span class="icon">
                        <span class="iconify" data-icon="tabler:friends"></span>
                    </span>
                    <span class="title">Подписчики</span>
                </a>
            </li>
            <li class="list">
                <a href="/users">
                    <span class="icon">
                        <span class="iconify" data-icon="ph:users-three"></span>
                    </span>
                    <span class="title">Пользователи</span>
                </a>
            </li>
            <li class="list active">
                <a href="/messages">
                    <span class="icon">
                        <span class="iconify" data-icon="ant-design:message-outlined"></span>
                    </span>
                    <span class="title">Сообщения</span>
                </a>
            </li>
            <li class="list">
                <a href="/settings">
                    <span class="icon">
                        <span class="iconify" data-icon="carbon:settings"></span>
                    </span>
                    <span class="title">Настройки</span>
                </a>
            </li>
            <li class="list">
                <a href="/help">
                <span class="icon">
                        <span class="iconify" data-icon="bx:bx-help-circle"></span>
                    </span>
                    <span class="title">Помощь</span>
                </a>
            </li>
            <?php if($_SESSION["user"]["activity"] == 1) {
                    echo ' <li class="list">
                    <a href="/admin">
                    <span class="icon">
                            <span class="iconify" data-icon="ic:outline-admin-panel-settings"></span>
                        </span>
                        <span class="title">Админ панель</span>
                    </a>
                </li>'; }?>
            <div class="toggle <?= $_COOKIE["nav-toggle"] == 1 ? 'active' : "" ?>">
            <span class="iconify" data-icon="akar-icons:chevron-left"></span>
            <span class="iconify" data-icon="akar-icons:chevron-left"></span>
        </div>
    </ul>
</aside>        </div>



        <main class="main">
            <div class="content__wrapper">
                <div class="messages">
                        <div class="messages__wrapper">
                            <div class="dialogs__inner">
                                <div class="dialogs__content">
                                <div class="chat__header">
                                        <a href="/main">
                                        <span class="iconify" data-icon="ant-design:home-outlined"></span>
                                        </a>
                                        <p><?= $_SESSION["user"]["login"] ?></p>
                                    </div>

                                    <div class="dialogs">
                                    <div class="chat-search">
                                        <div class="dialogs__search">
                                            <input type="text" name="" id="" placeholder="Поиск" class="dialogs__search-input">
                                        </div>
                                    </div>
                                        <ul class="dialogs__list">

                                            <?= empty($dialogs) ? "У вас нет диалогов" : '' ?>

                                            <?php foreach($dialogs as $dialog) : ?>
                                            
                                            <li class="dialogs__item" data-dialogs="<?=$dialog['chat_id'] ?>" <?= $dialog["is_read"] == 1 ? "style='background: rgb(255, 242, 207); border: 1px solid rgb(255, 218, 117);'"  : '' ?>>
                                            <img src="<?= ($dialog["avatar"] == 0) ? '/resource/noavatar.jpg' :  '/resource/users/'. $dialog["id_user"] . '/avatar/' . $dialog["avatar"] ?>" alt="">
                                                <div class="user__info">
                                                    <h5 class="username"><?= $dialog['name'] . ' ' . $dialog['surname'] ?></h5>
                                                    <p class="dialogs__text">
                                                    <?php foreach($lastMessages as $lastMessage) : ?>
                                                        <?= $lastMessage['chat_id'] == $dialog['chat_id'] &&  $_SESSION["user"]["id_user"] == $lastMessage["id_user"] ? "Вы: " : ""?>
                                                        <?= $lastMessage['chat_id'] == $dialog['chat_id'] ? strlen($lastMessage['content']) > 49 ? substr($lastMessage['content'], 0, 50) . '..' : $lastMessage['content'] : '' ?>
                                                    <?php endforeach; ?>
                                                    </p>
                                                    <?= $dialog["count_status"] > 0 ? '<span class="count__status">'. $dialog["count_status"] . '</span>' : "" ?>
                                                </div>
                                                <?php foreach($lastMessages as $lastMessage) : ?>
                                                    <span class="dialogs__date">
                                                        <?php $dialog_date = date_parse($lastMessage["date_create"]) ?>
                                                        <?php
                                                            $days = [
                                                                "",
                                                                "Января",
                                                                "Февраля",
                                                                "Марта",
                                                                "Апреля",
                                                                "Мая",
                                                                "Июня",
                                                                "Июля",
                                                                "Августа",
                                                                "Сентябя",
                                                                "Октября",
                                                                "Ноября",
                                                                "Декабря",
                                                            ];?>
                                                        <?=$lastMessage["chat_id"] == $dialog["chat_id"] ? $dialog_date["day"] . " " . $days[$dialog_date["month"]] : "" ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </li>
                                        
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>


                                <div class="messages__window">
                                    <div class="chat__header">
                                        <div class="back-dialogs">
                                            <span class="iconify" data-icon="akar-icons:arrow-left"></span>
                                        </div>
                                        <div class="chat__action-btn">
                                            <div class="delete-chat">
                                                удалить чат
                                            </div>
                                        </div> 
                                    </div>
                                    <div class="chat__body" id="chat__body">
                                        <div class="chat" id="chat">


                                           
                                        </div>
                                    </div>        
                                    <form name="messagebody" class="messages__sendbox" id="ajax_message">
                                        <label for="message">
                                            <textarea type="text" name="message" id="message-write" placeholder="Напишите что-нибудь" class="message__inp"></textarea>
                                            <button class="message__btn-send" id="message___btn-send" type="submit">
                                                    <ion-icon name="send-outline"></ion-icon>
                                            </button> 
                                        </label>
                                    </form>
                                </div>

                            </div>    
                        </div>
                </div>
            </div>
        </main>

    </div>
    <div class="shadow-data" data-id="<?= $_SESSION["user"]["id_user"] ?>"></div>      

                                                
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script> 
    <script src="https://code.iconify.design/2/2.1.0/iconify.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script type="text/javascript" src="../js/jquery.timeago.js"></script>
    <script type="text/javascript" src="../js/jquery.timeago.ru.js"></script>

    <script src="../js/search-dialog.js"></script>
    <script src="../js/chat-script.js"></script>
    <script src="../js/nav-script.js"></script>
    <script src="browser.js"></script>  
</body>
</html>