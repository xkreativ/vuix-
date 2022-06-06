<?php

    session_start();
    if(isset($_SESSION["auth"]) && $_SESSION["auth"] !== true) {
        header('Location: /login');
    }
    if(!isset($_SESSION["auth"])) {
        header('Location: /');
    }

    require_once 'vendor/db.php';

    $prewweek = date("Y-m-d H:i:s", time()-60*60*24*30);
    $nowdate = date("Y-m-d H:i:s");
    // Получаем 6 постов, начиная с последней отображенной
    $friendPosts = $db->prepare("SELECT 
	posts.id_post, posts.text_post, posts.photo, posts.created_at, 
    users.id_user, users.login, users.name, users.surname, users.avatar as avatar,
    c.text, c.added, c.date, u.name as enemy_name, u.surname as enemy_surname, u.login as enemy_login, u.avatar as enemy_avatar,
    lp.id as likeCheck, cf.countfld as countfld, uc.last_cid,
    COALESCE(`likes_post`, 0) AS `likes`,
    COALESCE(`comments`, 0) AS `comms`
FROM `posts` 
INNER JOIN `users_connections` ON posts.id_user = users_connections.id_followed 
INNER JOIN `users` ON users.id_user = posts.id_user
LEFT JOIN (
	SELECT `id_post`, COUNT(`likes_post`.`id_post`) `likes_post`
  	FROM `likes_post`
  	GROUP BY `id_post`
) `likes_post` ON `likes_post`.`id_post` = `posts`.`id_post`
LEFT JOIN (
	SELECT `id_post`, COUNT(`comments`.`id_post`) `comments`
  	FROM `comments`
  	GROUP BY `id_post`
) `comments` ON `comments`.`id_post` = `posts`.`id_post`
LEFT JOIN (
	SELECT `id_post`, `id`
  	FROM `likes_post`
    WHERE likes_post.id_user = :id_follower
    GROUP BY  likes_post.id
) `lp` ON `lp`.`id_post` = `posts`.`id_post`

left join(
  SELECT
    comments.id_post,
    max(comments.id) as last_cid
  FROM comments
  group by comments.id_post
)uc on posts.id_post=uc.id_post


LEFT JOIN (
	SELECT users_connections.id_followed, COUNT(`id_follower`) as countfld FROM `users_connections` GROUP BY id_followed
) `cf` ON `cf`.`id_followed` = `users`.`id_user`
left join comments c

  on c.id=uc.last_cid
left join users u
  on u.id_user=c.added
  
WHERE users_connections.id_follower = :id_follower AND `created_at` BETWEEN '$prewweek' AND '$nowdate'
ORDER BY `created_at`");
    $friendPosts->execute([
    "id_follower" => $_SESSION['user']['id_user'],
    ]);
    $friendPosts = $friendPosts->fetchAll();

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

        <div class="navbar">
        <aside class="navigation <?= $_COOKIE["nav-toggle"] == 1 ? 'active' : "" ?>">
    <div class="logo">VUIX</div>
    <ul>
        <li class="list active">
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
            <h2 class="page__title_main">Новостная лента</h2>
                <div class="home-wrapper">
                    <div class="home-content">
                        <div class="user-posts">
                        <?= count($friendPosts) == 0 ? '<div class="not-news"> <span class="iconify" data-icon="tabler:news-off"></span> <br> Новостей нет <br> Подпишитесь на кого-нибудь, либо вернитесь позже </div>' : ""  ?>
                        <?php foreach($friendPosts as $friendPost) : ?>
                            <div class="user-post">
                                <div class="user-about">
                                    <div class="user-photo">
                                        <a href="<?= $friendPost["login"] ?>">
                                            <img src="<?= ($friendPost["avatar"] == 0) ? '/resource/noavatar.jpg' :  '/resource/users/'. $friendPost["id_user"] . '/avatar/' . $friendPost["avatar"] ?>" alt="">
                                        </a>
                                    </div>
                                    <div>
                                        <div class="username">
                                            <a href="<?= $friendPost["login"] ?>">
                                                <?= $friendPost["surname"] . ' ' . $friendPost["name"] ?>
                                            </a>
                                        </div>
                                        <div class="user-subscribers">
                                            <?= $friendPost["countfld"] ?> подписчиков
                                        </div>
                                    </div>
                                </div>
                                <div class="user-text">
                                    <?= $friendPost["text_post"] ?>
                                </div>
                                <div class="post-image">
                                    <img src="<?= '/resource/users/'. $friendPost['id_user'] . '/posts/' . $friendPost["photo"] ?>"  data-link="<?= $friendPost["id_post"]   ?>" alt="">
                                </div>
                                <div class="post-counters">
                                    <div class="user-info-like-count user-info-count <?= $friendPost["likeCheck"] ? "like" : "" ?>" data-post="<?= $friendPost["id_post"] ?>">
                                        <span class="iconify" data-icon="ant-design:heart-fill"></span>
                                        <p><?= $friendPost["likes"] ?></p> лайков                                                
                                    </div>
                                    <div class="user-info-comm-count user-info-count">
                                        <span class="iconify" data-icon="icon-park-outline:comments"></span>
                                        <p><?= $friendPost["comms"] ?> комментариев</p>
                                    </div>
                                </div>
                                <div class="user-comments">
                                    <?php
                                                        if($friendPost["added"]) { ?>
                                        <div class="user-comment">
                                            <div class="user-comment-photo"> 
                                                <a href="<?= $friendPost["enemy_login"] ?>"> 
                                                    <img src="<?= ($friendPost["enemy_avatar"] == 0) ? '/resource/noavatar.jpg' :  '/resource/users/'. $friendPost["added"] . '/avatar/' . $friendPost["enemy_avatar"] ?>" alt="" >
                                                </a> 
                                            </div>
                                            <div class="comment__content"> 
                                                <a href="<?= $friendPost["enemy_login"] ?>">
                                                    <div class="user-comment-name"><?= $friendPost["enemy_login"] ?></div> 
                                                </a> 
                                                <div class="user-comment-text">
                                                    <?= $friendPost["text"] ?>
                                                </div>
                                                <div class="user-subcontent">
                                                
                                                    <time class="user-timeago" title="">Пользователь оставил комментарий: <?= $friendPost["date"] ?></time>
                                                    <div class="user-comment-action"></div>
                                                     
                                                </div>
                                            </div>
                                        </div>
                                        <?= $friendPost["comms"] > 1 ? "<div class='more-comments' data-comment=" . $friendPost['last_cid'] . " data-post=" . $friendPost["id_post"] . " >показать больше комментариев</div>" : "" ?>
                                        <?php
                                            }
                                        ?>   
                                    </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>


            </div>
            <nav class="mobile-nav">
                <ul>
                    <li class="list">
                        <a href="/main">
                            <span class="icon">
                                <span class="iconify" data-icon="ant-design:home-twotone"></span>
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

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>  
    <script type="text/javascript" src="../js/jquery.timeago.js"></script>
    <script type="text/javascript" src="../js/jquery.timeago.ru.js"></script>

    <script src="../js/main.js"></script>
    <script src="../js/nav-script.js"></script>

</body>
</html>