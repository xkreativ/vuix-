<?php

    session_start();
    if(isset($_SESSION["auth"]) && $_SESSION["auth"] !== true) {
        header('Location: /');
    }
    if(!isset($_SESSION["auth"])) {
        header('Location: /');
    }

    require_once 'vendor/db.php';

    $users = $db->prepare("SELECT `id_user`, `name`, `surname`, `login`,  `avatar` FROM `users` ORDER BY `id_user` DESC LIMIT 7");
    $users->execute([
    ]);
    $users = $users->fetchAll();
    
    $countusers = $db->prepare("SELECT COUNT(`id_user`) AS count FROM `users`");
    $countusers->execute([
    ]);
    $countusers = $countusers->fetchAll();

    $help = $db->prepare("SELECT  users.name, users.surname, users.login, users.avatar, help.id_help, help.answer, help.id_user, help.text, help.created_at
    FROM `help` JOIN `users` ON help.id_user = users.id_user WHERE help.id_user = :id_user AND help.answer != '' ORDER BY `created_at` DESC ");
    $help->execute([
        "id_user" => $_SESSION["user"]["id_user"],
    ]);
    $help = $help->fetchAll();

    $help = $db->prepare("SELECT users.name, users.surname, users.login, users.avatar, help.id_help, help.answer, help.id_user, help.text, help.created_at
    FROM `help` JOIN `users` ON help.id_user = users.id_user WHERE help.id_user = :id_user AND help.answer != '' ORDER BY `created_at` DESC ");
    $help->execute([
        "id_user" => $_SESSION["user"]["id_user"],
    ]);
    $help = $help->fetchAll();
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
    <title> Помощь </title>
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
            <li class="list active">
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
</aside>
        </div>



        <main class="main">
            <div class="content__wrapper">

                <section class="help">
                    <div class="help__content">
                        <form action="../vendor/help.php" class="help__form" method="POST" enctype="multipart/form-data">
                            <textarea name="helptext" id="" cols="30" rows="10" placeholder="Задайте интересующий вас вопрос"></textarea>
                            <button type="submit">отправить</button>
                        </form>
                        <img src="../img/help.png" alt="">
                    </div>
                    <div class="help__requests">
                        <section class="admin__help">
                        <?php foreach($help as $he) : ?>
                        <div class="admin__help_block" data-request="<?= $he["id_help"] ?>">
                           <div class="help-user-question">
                               <div class="help-user-info">
                                   <div class="help-user-photo">
                                       <img src="<?= ($he["avatar"] == 0) ? '/resource/noavatar.jpg' :  '/resource/users/'. $he["id_user"] . '/avatar/' . $he["avatar"]  ?>" alt="">
                                   </div>
                                   <div class="help-user-about">
                                       <p class="username"><?= $he["surname"] . ' ' . $he["name"] ?></p>
                                       <p class="nickname"><?= $he["login"] ?></p>
                                   </div>
                                   <div class="admin__help_btn-box">
                                        <button class="admin__help_btn admin__help_btn-delete" data-help="<?= $he["id_help"]?>">удалить</button>
                                    </div>
                               </div>
                               <div class="help-user-text">
                                    <?= $he["text"]?>
                               </div>
                           </div>
                           <div class="help-user-answer" data-answer="<?= $he["id_help"]?>">
                                <?= $he["answer"] ?>
                           </div>
                           <div class="help-date-create"><?= $he["created_at"]?></div>
                        </div>
                        <?php endforeach; ?> 
                        <?php if (count($help) == 0) {
                          echo '<div class="help__norequests">
                          <div class="text">Ответов пока нет, ожидайте в течение 12 дней</div>
                      </div>';
                      } ?>
                        <img src="../img/calendar.png" alt="" class="help-calendar">
                        </section>
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

    <script src="https://code.iconify.design/2/2.1.0/iconify.min.js"></script>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>

    <script src="../js/search.js"></script>
    <script src="../js/admin-script.js"></script>
    <script src="../js/nav-script.js"></script>
</body>
</html>