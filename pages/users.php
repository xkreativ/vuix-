<?php

    session_start();
    if(isset($_SESSION["auth"]) && $_SESSION["auth"] !== true) {
        header('Location: /');
    }
    if(!isset($_SESSION["auth"])) {
        header('Location: /');
    }

    require_once 'vendor/db.php';
    
    $users = $db->prepare("SELECT posts.*, users.id_user, users.name, users.surname, users.login, users.avatar
    FROM (
       SELECT id_post AS 'maxid' FROM `posts`
        WHERE id_user != :id_user
        GROUP BY id_user, id_post
          ORDER BY rand()
                LIMIT 50
    ) AS a
    INNER JOIN posts ON a.maxid = id_post
    JOIN users ON posts.id_user = users.id_user
    ORDER BY RAND()");
    $users->execute([
    "id_user" => $_SESSION["user"]["id_user"],
    ]);
    $users = $users->fetchAll();

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
    <title>Пользователи</title>
        <link rel="shortcut icon" href="/favicon.png" type="image/png">
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
            <li class="list active">
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
            <div class="header">
                <div class="header__inner">
                <div class="search">
                <label>
                    <input type="text" name="search-users" class="who" placeholder="Поиск на Vuix" autocomplete="off">
                    <ul class="search_result"></ul>
                    <span class="iconify" data-icon="eva:search-fill"></span>
                </label>
            </div>
                </div>
            </div>
            <div class="content__wrapper">
                <div class="users__items">
                <?php foreach($users as $user) : ?>
                    <div class="users__item">
                        <div class="users__item_img">
                                <img src="<?= '/resource/users/'. $user['id_user'] . '/posts/' . $user["photo"] ?>" data-link="<?= $user["id_post"] ?> " data-user="<?= $user["id_user"] ?>" alt="">
                            <div class="users__info">
                                <div class="users__info_photo">
                                    <a href="/<?= $user["login"] ?>">
                                        <img src="<?= ($user["avatar"] == 0) ? '/resource/noavatar.jpg' :  '/resource/users/'. $user["id_user"] . '/avatar/' . $user["avatar"] ?>" alt="">
                                    </a>
                                </div>
                                <div class="users__info_name">
                                <a href="/<?= $user["login"] ?>">
                                    <?= $user["surname"] . ' ' . $user["name"] ?>
                                </a>
                                </div>
                            </div>
                        </div>
                    </div>    
                    <?php endforeach; ?>
                </div>
                    
                <section class="modal">
                    <div class="modal__inner">
                       <div class="modal__content">
                                <div class="modal__user">
                                    <div class="modal__user_about">
                                        <div class="user__image">
                                            <a href="">
                                                <img src="<?= $_SESSION["user"]["avatar"] == '0' ? '/resource/noavatar.jpg' : 'resource/users/' . $_SESSION["user"]["id_user"] . '/avatar/'. $_SESSION["user"]["avatar"] .'' ?>" alt="">                                   </div>
                                            </a>
                                        <div class="username">
                                            <a href="">
                                                <p class="count-sub"></p>
                                            </a>
                                        </div>
                                    </div> 
                                    <div class="close-modal">
                                        <span></span>
                                    </div>
                                </div>
                            <div class="modal__image">
                                <img src="" alt="">
                            </div>
                            <div class="modal__descr">
                            <div class="post__reactions">
                                    <div class="post__reactions-item like">
                                        <div class="post__reactions-icon btnLike"><span class="iconify" data-icon="ant-design:heart-outlined"></span></div>
                                        <div class="post__reactions-count counter__likes"></div>
                                        <p>лайк</p>
                                    </div>
                                    <div class="who-liked-post">
                                        <span class="iconify" data-icon="clarity:help-line"></span>
                                    </div>
                                    <div class="post__reactions-item">
                                        <div class="post__reactions-icon"><span class="iconify" data-icon="icon-park-outline:comments"></div>
                                        <div class="post__reactions-count" id="post__reactions-count-comments"></div>
                                        <p>комментариев</p>
                                    </div>
                            </div>
                                <div class="modal__text" id="user__comment">
                                </div>
                                <div class="modal__content-date" id="user__date">
                                </div>
                            
                           </div>
                       </div>
                       <div class="modal__comments">
                            <div class="title">Комментарии</div>
                            <div class="comments__body" id="comments__body">
                                
                            </div>
                            <form action="" class="comment__write_box" id="ajax_comment">
                                <textarea type="text" name="comment" id="comment-write" placeholder="Написать сообщение"></textarea>
                                <button class="comment__write_btn" id="comment__write_btn" type="submit">
                                    <span class="iconify" data-icon="bx:bxs-send"></span>
                                </button>    
                            </form>
                       </div>
                    </div>
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
                                <span class="iconify" data-icon="ph:magnifying-glass-duotone"></span>
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

    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script> 

    <script src="https://code.iconify.design/2/2.1.0/iconify.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
    <script type="text/javascript" src="../js/jquery.timeago.js"></script>
    <script type="text/javascript" src="../js/jquery.timeago.ru.js"></script>

    <script src="../js/search-users.js"></script>
    
    <script src="../js/users.js"></script>
    <script src="../js/nav-script.js"></script>

</body>
</html>