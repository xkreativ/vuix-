<?php

    session_start();
    if(isset($_SESSION["auth"]) && $_SESSION["auth"] !== true) {
        header('Location: /');
    }
    if(!isset($_SESSION["auth"])) {
        header('Location: /');
    }

    require_once 'vendor/db.php';


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
    <title> Настройки аккаунта </title>
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
            <li class="list active">
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
</aside>
        </div>



        <main class="main">
            <div class="content__wrapper">
            <h2 class="page__title_settings">Настройки аккаунта</h2>

            <section class="settings">
                <div class="settings__inner">
                    <form action="/vendor/edit.php" method="POST" enctype="multipart/form-data">
                        <div class="bio">
                            <div class="settings__profile_preview">
                                <div class="profile__photo">
                                <img id="downImg" src="<?= ($_SESSION["user"]["avatar"] == 0) ? '/resource/noavatar.jpg' :  '/resource/users/'. $_SESSION["user"]["id_user"] . '/avatar/' . $_SESSION["user"]["avatar"] ?>" alt="Аватар пользователя" class="user-avatar">
                                    <input type="file" class="file" name="avatar" id="imgInp">
                                </div>
                            </div>
                    
                            <div class="settings__profile_info">
                                <label>
                                    <input type="text" name="name" class="input-info__main" value="<?= $_SESSION["user"]["name"]   ?>">
                                    <span>(здесь вы можете изменить своё имя)</span>
                                </label>
                                <label>
                                    <input type="text" name="surname" class="input-info__main" value="<?= $_SESSION["user"]["surname"]  ?>">
                                    <span>(здесь вы можете изменить свою фамилию)</span>
                                </label>
                                <label>
                                <input type="text" name="login" class="input-info__main" autocomplete="off" value="<?= $_SESSION["user"]["login"] ?>">                            <span>(здесь вы можете изменить свой логин)</span>
                                </label>

                            </div>

                            <div class="profile__desc">  
                                <label> 
                                    <textarea name="info" class="textarea-info__desc" placeholder="Информация о вас:"><?=$_SESSION["user"]["info"]?></textarea>
                                    <span>(здесь вы можете изменить описание)</span>
                                </label>
                                <label> 
                                    <input type="text" name="link" class="textarea-info__link" placeholder="Ссылка на личный сайт:" value="<?= $_SESSION["user"]["link"]?>">
                                    <span>(здесь вы можете изменить ссылку на личный сайт)</span>
                                </label>
                            </div>

                            <div class="settings__password">
                                <h3 class="title">Смена пароля</h3>

                                <div class="set-password__box">
                                    <input type="password" name="opassword" class="set-password" placeholder="Старый пароль:">
                                    <input type="password" name="npassword" class="set-password" placeholder="Новый пароль:">
                                </div>
                            </div>

                            <div class="settings__btn_box">
                                <button class="settings__btn-action settings__btn-submit" type="submit">cохранить</button>
                                <button class="settings__btn-action settings__btn-reset" type="reset">сбросить</button>
                            </div>
                        
                        </div>
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
                    </form>
                    <!-- <nav class="settings__nav">
                        <ul>
                            <div class="title">Настройки</div>
                            <li> <span class="active"></span> Основные</li>
                            <li> <span></span> Дополнительный</li>
                        </ul>
                        <ul>
                            <div class="title">Информация</div>
                            <li> <span></span> Об аккаунте</li>
                            <li> <span></span> Понравилось</li>
                        </ul>
                        <ul>
                            <div class="title">Помощь</div>
                            <li> <span></span> Помощь</li>
                        </ul>
                    </nav> -->
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
                                <span class="iconify" data-icon="eva:settings-fill"></span>
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
    <script src="../js/admin_search.js"></script>

    <script src="../js/nav-script.js"></script>
    <script src="../js/settings.js"></script>
</body>
</html>