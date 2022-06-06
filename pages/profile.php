<?php

    session_start();
    if(isset($_SESSION["auth"]) && $_SESSION["auth"] !== true) {
        header('Location: /');
    }
    if(!isset($_SESSION["auth"])) {
        header('Location: /');
    }

    require_once 'vendor/db.php';
    
    /*------------------ Запрос на публикации пользователя ----------------------*/
    $posts = $db->prepare("SELECT * FROM `posts` WHERE  `id_user` = :id_user ORDER BY `created_at` DESC LIMIT 6 ");
    $posts->execute([
    "id_user" => $_SESSION["user"]["id_user"],
    ]);
    $posts = $posts->fetchAll();

    /*------------------ Запрос на количество публикаций пользователя ----------------------*/
    $countPosts = $db->prepare("SELECT COUNT(id_post) FROM `posts` WHERE  `id_user` = :id_user");
    $countPosts->execute([
    "id_user" => $_SESSION["user"]["id_user"],
    ]);
    $countPosts = $countPosts->fetch();

    /*------------------ Запрос на подписчиков ----------------------*/
    $subscribers = $db->prepare("SELECT `id_follower` FROM `users_connections` WHERE `id_followed` = :id_followed");
    $subscribers->execute([
    "id_followed" => $_SESSION["user"]["id_user"],
    ]);
    $subscribers = $subscribers->fetchAll();

    /*------------------ Запрос на подписки ----------------------*/
    $subscriptions = $db->prepare("SELECT `id_followed` FROM `users_connections` WHERE `id_follower` = :id_follower");
    $subscriptions->execute([
    "id_follower" => $_SESSION["user"]["id_user"],
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
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://code.iconify.design/2/2.1.0/iconify.min.js"></script>
    <title>Мой профиль</title>
        <link rel="shortcut icon" href="/favicon.png" type="image/png">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    

    <div class="wrapper">
        <div class="to-help-page">
            <a href="/help">
                <span class="iconify" data-icon="dashicons:editor-help"></span>
            </a>
        </div>
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
                        <img src="<?= ($_SESSION["user"]["avatar"] == 0) ? '/resource/noavatar.jpg' :  '/resource/users/'. $_SESSION["user"]["id_user"] . '/avatar/' . $_SESSION["user"]["avatar"] ?>" alt="" class="user-avatar">
                        </div>
                    </div>
            
                    <div class="profile__info">
                        <h2 class="username"> 
                            <?= $_SESSION["user"]["surname"] .' '. $_SESSION["user"]["name"] ?>
                         </h2>
                         <div class="counts__module">
                        <a href="#" class="counter" id="post__counter">
                            <div class="count"><?= $posts ? $countPosts[0] : 0;?></div>
                            <div class="label">Публикаций</div>
                        </a>
                        <a href="/friends?section=subscribers" class="counter">
                            <div class="count"><?= $subscribers ? count($subscribers) : 0;?></div>
                            <div class="label">Подписчиков</div>
                        </a>
                        <a href="/friends?section=subscriptions" class="counter">
                            <div class="count"><?= $subscriptions ? count($subscriptions) : 0;?></div>
                            <div class="label">Подписки</div>
                        </a>
                    </div>
                        <p class="text"><?= $_SESSION["user"]["info"]?></p>
                        <a href="<?= $_SESSION["user"]["link"]?>" target="_blank" class="link"><?= $_SESSION["user"]["link"]?></a>
                    </div>
                    
                </div>

                <section class="publish">
                    <h3 class="title">Публикации<span class="add-publish-btn"></span></h3>

                <form action="/vendor/newpost.php" method="post" enctype="multipart/form-data" class="post-form">

                    <div class="new-post-wrapper none">
                    <?php if(isset($_SESSION['is_error']) && $_SESSION['is_error'] === true){
                            ?>
                                <div class="alert alert-danger">
                                    <?= $_SESSION['error_message'] ?>
                                </div>
                            <?php
                            }
                            unset($_SESSION['is_error']);
                            unset($_SESSION['error_message']);
                    ?>
                        <div class="post-image-wrapper">
                            <div class="post-image-text">Загрузите изображение</div>
                            <img  alt=""  class="new-post-image" id="post-img"> 
                            <input type="file" class="file" id="post-inp" name="image">
                        </div>
                        <textarea name="text" id="" class="new-post-descr" placeholder="Напишите комментарий к фотографии:"></textarea>
                        <button class="new-post-btn btn-action">добавить публикацию</button>
                    </div>

                </form>

                    <div class="posts__wrapper" id="posts__wrapper">
                        <?php foreach($posts as $post) : ?>
                                <div class="user__post">
                                    <img src="<?= '/resource/users/'. $_SESSION["user"]["id_user"] . '/posts/' . $post["photo"] ?>" data-link="<?= $post["id_post"] ?>" alt="Изображение">
                                    <div class="user__post-delete" data-delete="<?= $post["id_post"] ?>">
                                    </div>
                                </div>
                        <?php endforeach; ?>
                    </div>

                        <?php if ($countPosts[0] == 0) {
                            echo '<div class="noposts"><span class="iconify" data-icon="ant-design:picture-outlined"></span><p>У вас нет публикаций</p></div>';

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
                                            <img src="<?= $_SESSION["user"]["avatar"] == '0' ? '/resource/noavatar.jpg' : 'resource/users/' . $_SESSION["user"]["id_user"] . '/avatar/'. $_SESSION["user"]["avatar"] .'' ?>" alt="">                                   </div>
                                        <div class="username">
                                            <?= $_SESSION["user"]["surname"] . ' ' . $_SESSION["user"]["name"] ?>
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
    <div class="shadow-data" data-id="<?= $_SESSION["user"]["id_user"] ?>"></div>

    <script src="https://code.iconify.design/2/2.1.0/iconify.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
    <script type="text/javascript" src="../js/jquery.timeago.js"></script>
    <script type="text/javascript" src="../js/jquery.timeago.ru.js"></script>
    
    <script src="../js/account.js"></script>
    <script src="../js/profile-script.js"></script>
    <script src="../js/nav-script.js"></script>

</body>
</html>