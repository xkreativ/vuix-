<?php

    session_start();
    if(isset($_SESSION["auth"]) && $_SESSION["auth"] !== true) {
        header('Location: /');
    }
    if(!isset($_SESSION["auth"])) {
        header('Location: /');
    }

    require_once 'vendor/db.php';
 
    /*----------------------- Проверка на существование страницы и пользователя------------------------------ */
    if($Page) {
        $info = $db->prepare("SELECT `id_user`, `name`, `surname`, `login`, `activity`, `info`, `link`, `avatar` FROM `users` WHERE `login` = :login");
        $info->execute([
        "login" => $Page,
    ]);
        $info = $info->fetch();

        if(!$info['id_user']) {
            header('Location: /profile');
        }
        if($info['id_user'] == $_SESSION['user']['id_user']) {
            header('Location: /profile');
        }
    } 

    /*------------------ Запрос на количество публикаций пользователя ----------------------*/
    $countPosts = $db->prepare("SELECT COUNT(id_post) FROM `posts` WHERE  `id_user` = :id_user");
    $countPosts->execute([
    "id_user" => $info['id_user'],
    ]);
    $countPosts = $countPosts->fetch();

    /*------------------ Запрос на публикации пользователя ----------------------*/
    $posts = $db->prepare("SELECT * FROM `posts` WHERE `id_user` = :id_user ORDER BY `created_at` DESC LIMIT 6");
    $posts->execute([
    "id_user" => $info['id_user'],
    ]);
    $posts = $posts->fetchAll();

    /*------------------ Запрос на подписчиков ----------------------*/
    $subscribers = $db->prepare("SELECT `id_follower` FROM `users_connections` WHERE `id_followed` = :id_followed");
    $subscribers->execute([
    "id_followed" => $info['id_user'],
    ]);
    $subscribers = $subscribers->fetchAll();

    /*------------------ Запрос на подписки ----------------------*/
    $subscriptions = $db->prepare("SELECT `id_followed` FROM `users_connections` WHERE `id_follower` = :id_follower");
    $subscriptions->execute([
    "id_follower" => $info['id_user'],
    ]);
    $subscriptions = $subscriptions->fetchAll();
?>




<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@100;200;300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title><?= $info['surname'] .' '. $info['name'] ?></title>
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
            <li class="list active">
                <a href="/profile active">
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
            <div class="content__wrapper">

                <div class="bio">
                    <div class="profile__preview">
                        <div class="profile__photo">
                         <img src="<?= ($info["avatar"] == 0) ? '/resource/noavatar.jpg' :  '/resource/users/'. $info["id_user"] . '/avatar/' . $info["avatar"]?>" alt="" class="user-avatar">
                        </div>
                    </div>
            
                    <div class="profile__info">
                        <h2 class="username"> 
                            <?= $info['surname'] .' '. $info['name'] ?>
                        
                         </h2>
                         <?php if($_SESSION["user"]["activity"] == 1) {
                                echo '<a href="/admin"> <span data-idUser="' . $info['id_user'] . '" id="delete-user"> удалить пользователя</span></a>';
                            }?> 
                        <div class="counts__module">
                            <a href="/friends?<?= 'id=' . $info['id_user'] ?>&section=all" class="counter" id="post__counter">
                                <div class="count"><?= $posts ? $countPosts[0] : 0;?></div>
                                <div class="label">Публикаций</div>
                            </a>
                            <a href="/friends?<?= 'id=' . $info['id_user'] ?>&section=subscribers" class="counter">
                                <div class="count"><?= $subscribers ? count($subscribers) : 0;?></div>
                                <div class="label">Подписчиков</div>
                            </a>
                            <a href="/friends?<?= 'id=' . $info['id_user'] ?>&section=subscriptions" class="counter">
                                <div class="count"><?= $subscriptions ? count($subscriptions) : 0;?></div>
                                <div class="label">Подписки</div>
                            </a>
                        </div>
                        <p class="text"><?= $info['info']?></p>
                        <a href="#" class="link"><?= $info['link']?></a>
                        <div class="action-buttons">
                        <?php 
                            /*------------------ Запрос на проверку подписан ли на пользователя----------------------*/
                            $followCheck = $db->prepare("SELECT * FROM `users_connections` WHERE `id_follower` = :id_follower AND `id_followed` = :id_followed");
                            $followCheck->execute([
                            "id_follower" => $_SESSION["user"]["id_user"],
                            "id_followed" => $info['id_user'],
                            ]);
                            $followCheck = $followCheck->fetch();

                            if(!$followCheck) {
                                ?>
                                <form action="/vendor/connection.php" method="post">
                                <button class="btn__action-sub in">
                                    подписаться
                                </button>
                                <input value="<?= $info['id_user'] ?>" type="text" name="id_followed" class="none">
                                <input value="<?= $info['login'] ?>" type="text" name="login" class="none">
                                </form> 
                                <?php
                            } else {
                                ?> 
                                <form action="/vendor/connection.php" method="post">
                                <button class="btn__action-sub">
                                    отписаться
                                </button>
                                    <input value="<?= $info['id_user'] ?>" type="text" name="id_followed" class="none">
                                    <input value="<?= $info['login'] ?>" type="text" name="login" class="none">
                                </form>
                                <?php
                            }
                            ?>   
                                <button id="write__message_btn" data-message="<?=$info['id_user']?>">сообщение</button>
                        </div>
                    </div>
                </div>

                <section class="publish">
                    <h3 class="title">Публикации</h3>

                    <div class="posts__wrapper" id="posts__wrapper">
                        <?php foreach($posts as $post) : ?>
                                <div class="user__post">
                                    <img src="<?= '/resource/users/'. $info['id_user'] . '/posts/' . $post["photo"] ?>"  data-link="<?= $post["id_post"] ?>" alt="Изображение">
                                    <?php
                                    if($_SESSION["user"]["activity"] == 1) { ?>
                                        <div class="user__post-delete" data-delete="<?= $post["id_post"] ?>">
                                        <?
                                    }
                                    ?>
                                    </div>
                                </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if ($countPosts[0] == 0) {
                        echo '<div class="noposts"><span class="iconify" data-icon="ant-design:picture-outlined"></span><p>Данный пользователь пока что не выкладывал публикаций</p></div>';
                    } ?>
                      <?php if ($countPosts[0] > 6) {
                          echo '<div class="btn-wrap"> <button class="more" id="more-posts">ещё публикации</button> </div>';
                      } ?>
                </section>

                <section class="modal">
                    <div class="modal__inner">
                       <div class="modal__content">
                                <div class="modal__user">
                                    <div class="modal__user_about">
                                        <div class="user__image">
                                            <img src="<?= $info['avatar'] == '0' ? '/resource/noavatar.jpg' : 'resource/users/' . $info['id_user'] . '/avatar/'. $info['avatar'] .'' ?>" alt="">                          
                                        </div>
                                        <div class="username">
                                            <?= $info["surname"] . ' ' . $info["name"] ?>
                                            <p><?= $subscribers ? count($subscribers) : 0;?> подписчиков</p>
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
        </main>
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
    </div>      
    <div class="shadow-data" data-id="<?= $info["id_user"] ?>"></div>



    <script src="https://code.iconify.design/2/2.1.0/iconify.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script type="text/javascript" src="../js/jquery.timeago.js"></script>
    <script type="text/javascript" src="../js/jquery.timeago.ru.js"></script>

    <script src="../js/search.js"></script>

    <script src="../js/account.js"></script>
    <script src="../js/user-script.js"></script>
    <script src="../js/nav-script.js"></script>
</body>
</html>