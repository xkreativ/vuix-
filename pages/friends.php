<?php

    session_start();
    if(isset($_SESSION["auth"]) && $_SESSION["auth"] !== true) {
        header('Location: /');
    }
    if(!isset($_SESSION["auth"])) {
        header('Location: /');
    }

    require_once 'vendor/db.php';


    /* Запрос на поиск возможных друзей */
    $notmyFriend = $db->prepare("SELECT `id_followed` FROM `users_connections` WHERE id_follower = :id_user");
    $notmyFriend->execute([
    "id_user" => $_SESSION["user"]["id_user"],
    ]);
    $notmyFriend = $notmyFriend->fetchAll();
    $arr = [];
    foreach ($notmyFriend as $elem) {
        array_push($arr, $elem["id_followed"]);
    }

    $arr = array_unique($arr);
    $arr = implode(", ", $arr);


    /* Запрос на поиск возможных друзей */
    $mbfriends = $db->prepare("SELECT `id_user`, `login`, `name`, `surname`, `avatar` FROM `users` WHERE id_user != :id_user
    AND id_user NOT IN(:arr)
    AND `avatar` != 0
    ORDER BY RAND()
    LIMIT 4");
    $mbfriends->execute([
    "id_user" => $_SESSION["user"]["id_user"],
    "arr" => $arr,
    ]);
    $mbfriends = $mbfriends->fetchAll();


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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

    <title> Подписчики </title>
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
            <li class="list active">
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
            <li class="list">
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
            <div class="friend-content__wrapper">
            <h2 class="page__title_friends" id="page__title_friends">Подписчики</h2>
            <section class="friends">
                <div class="friends__list-wrap">
                <div class="friend-search">
                    <label>
                        <input type="text" name="search-users" class="search-input" placeholder="Поиск" autocomplete="off">
                        <span class="iconify" data-icon="eva:search-fill"></span>
                    </label>
                </div>
                    <div class="friends__list" id="friends__list">
                    </div>
                </div>
                <aside class="friends__rightcol">
                    <div class="friends__category">
                        <a href="section=subscribers" class="friends__category_link">
                            <div class="friends__category_item active">
                                Подписчики
                            </div>
                        </a>
                            <a href="section=subscriptions" class="friends__category_link">
                            <div class="friends__category_item">
                                <p>Подписки</p>
                            </div>
                        </a>
                    </div>
                    <div class="friends__recom">
                        <div class="friends__recom_title">Возможные друзья</div>
                        <div class="friend__recom_list">

                        <?php foreach($mbfriends as $mbfriend) : ?>

                            <div class="friend__recom_item">
                                <div class="friends__recom__image">
                                    <a href="/<?= $mbfriend["login"] ?>">
                                        <img src="<?= ($mbfriend["avatar"] == 0) ? '/resource/noavatar.jpg' :  '/resource/users/'. $mbfriend["id_user"] . '/avatar/' . $mbfriend["avatar"] ?>" alt="">
                                        <p class="friends__recom__name">
                                        <span><?= $mbfriend["name"] ?></span> 
                                        <span><?= $mbfriend["surname"] ?></span> 
                                        </p>
                                    </a>                                
                                </div>
                                
                            </div>
                            
                        <?php endforeach; ?>
                        </div>
                    </div>
                </aside>
            </section>

            </div>
            <nav class="mobile-nav">
                <ul>
                    <li class="list">
                        <a href="/main">
                            <span class="icon">
                                <span class="iconify" data-icon="ant-design:home-outlined"></span>
                            </span>    
                        </a>
                    </li>
                    <li class="list">
                        <a href="/users">
                            <span class="icon">
                                <span class="iconify" data-icon="ph:magnifying-glass-light"></span>
                            </span>    
                        </a>
                    </li>
                    <li class="list">
                        <a href="/messages">
                            <span class="icon">
                            <span class="iconify" data-icon="ant-design:message-outlined"></span>
                            </span>    
                        </a>
                    </li>
                    <li class="list"> 
                        <a href="/settings">
                            <span class="icon">
                                <span class="iconify" data-icon="eva:settings-outline"></span>
                            </span>     
                        </a>                   
                    </li>
                    <li class="list">
                        <a href="/profile">
                            <img class="user-logout-image" src="/resource/users/2/avatar/626a3a5f65686.jpg" alt="">
                        </a>
                    </li>
                </ul>
            </nav>
        </main>
    </div>      

    <script src="https://code.iconify.design/2/2.1.0/iconify.min.js"></script>

    <script src="../js/search.js"></script>

    <script src="../js/nav-script.js"></script>
    <script src="../js/friends.js"></script>
</body>
</html>