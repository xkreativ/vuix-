<?php

    require_once 'db.php';
    session_start();
    
    $login = $_POST['login'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $activity = $_POST['activity'];
    $info = $_POST['info'];
    $link = $_POST['link'];
    $opassword = $_POST['opassword'];
    $npassword = $_POST['npassword'];
    $avatar = $_POST['avatar'];

    /* Смена пароля пользователя */

    if ($opassword or $npassword) {
        if(!$opassword) {
            $_SESSION['is_error'] = true;
            $_SESSION['error_message'] = 'Не указан старый пароль';
            header('Location: /settings');
        }
        if(!$npassword) {
            $_SESSION['is_error'] = true;
            $_SESSION['error_message'] = 'Не указан новый пароль';
            header('Location: /settings');
        }
        if(!password_verify($opassword, $_SESSION["user"]["password"])) {
            $_SESSION['is_error'] = true;
            $_SESSION['error_message'] = 'Старый пароль указан не верно';
            header('Location: /settings');   
        } else if(strlen($npassword) < 4){
            $_SESSION['is_error'] = true;
            $_SESSION['error_message'] = 'Новый пароль должен содержать не менее 4 символов';
            header('Location: /settings');          
        } else {
            $app_user = $db->prepare("UPDATE `users` SET `password` = :password WHERE `id_user` = :id_user");
            $app_user->execute([
                "password" => password_hash($npassword, PASSWORD_DEFAULT),
                "id_user" => $_SESSION["user"]["id_user"],
            ]);
            $_SESSION["user"]["password"] = password_hash($npassword, PASSWORD_DEFAULT);
        }
    }

    /* Смена логина пользователя */

    if($login != $_SESSION["user"]["login"]) {
        if(preg_match("/^[a-z0-9_]{4,20}$/i", $login)) {
            $app_user = $db->prepare("UPDATE `users` SET `login` = :login WHERE `id_user` = :id_user");
            $app_user->execute([
                "login" => $login,
                "id_user" => $_SESSION["user"]["id_user"],
            ]);
            $_SESSION["user"]["login"] = $login;
        } else {
            $_SESSION['is_error'] = true;
            $_SESSION['error_message'] = 'Неправильный логин';
            header('Location: /settings');  
        }
    }

    /* Смена имени пользователя */
    
    if($name != $_SESSION["user"]["name"]) {
        if(preg_match("/^[a-zА-Я]{2,20}$/iu", $name)) {
            $app_user = $db->prepare("UPDATE `users` SET `name` = :name WHERE `id_user` = :id_user");
            $app_user->execute([
                "name" => $name,
                "id_user" => $_SESSION["user"]["id_user"],
            ]);
            $_SESSION["user"]["name"] = $name;
        } else {
            $_SESSION['is_error'] = true;
            $_SESSION['error_message'] = 'Неправильно введено Имя';
            header('Location: /settings');  
        }
    }

    /* Смена фамилии пользователя */

    if($surname != $_SESSION["user"]["surname"]) {
        if(preg_match("/^[a-zА-Я]{2,20}$/iu", $surname)) {
            $app_user = $db->prepare("UPDATE `users` SET `surname` = :surname WHERE `id_user` = :id_user");
            $app_user->execute([
                "surname" => $surname,
                "id_user" => $_SESSION["user"]["id_user"],
            ]);
            $_SESSION["user"]["surname"] = $surname;
        } else {
            $_SESSION['is_error'] = true;
            $_SESSION['error_message'] = 'Неправильно введено Имя';
            header('Location: /settings');  
        }
    }

    /* Смена информации о пользователе */

    if($info != $_SESSION["user"]["info"]) {
        if(strlen($info) < 600) {
            $app_user = $db->prepare("UPDATE `users` SET `info` = :info WHERE `id_user` = :id_user");
            $app_user->execute([
                "info" => $info,
                "id_user" => $_SESSION["user"]["id_user"],
            ]);
            $_SESSION["user"]["info"] = trim($info);
        } else {
            $_SESSION['is_error'] = true;
            $_SESSION['error_message'] = 'Информация о вас должна содержать меньше 500 символов';
            header('Location: /settings');  
        }
    }

     /* Смена информации о ссылке на личный сайт пользователя*/
     if($link != $_SESSION["user"]["link"]) {
        if(strlen($link) < 1000) {
            $app_user = $db->prepare("UPDATE `users` SET `link` = :link WHERE `id_user` = :id_user");
            $app_user->execute([
                "link" => $link,
                "id_user" => $_SESSION["user"]["id_user"],
            ]);
            $_SESSION["user"]["link"] = $link;
        } else {
            $_SESSION['is_error'] = true;
            $_SESSION['error_message'] = 'Ссылка слишком большая';
            header('Location: /settings');  
        }
    }

    /* Загрузка, проверка и изменение аватара */
    if($_FILES['avatar']['tmp_name']) {
        if ($_FILES['avatar']['size'] > 4 * 1024 * 1024) { 
            $_SESSION['is_error'] = true;
            $_SESSION['error_message'] = 'Размер файла превышает 2МБ';
            header('Location: /settings'); 
        }elseif ($_FILES['avatar']['type'] == 'image/jpeg') {

             /* Получаем расширение файла */

             $extensionFile = end(explode(".", $_FILES['avatar']['name']));

             /* Удаляем старый аватар */
             $path = $_SERVER['DOCUMENT_ROOT'] . '/resource/users/' .  $_SESSION["user"]["id_user"] . '/avatar/*';
            array_map('unlink', glob($path));

             /* Помещаем файл в папку */
             move_uploaded_file($_FILES['avatar']['tmp_name'], '../resource/users/'. $_SESSION["user"]["id_user"] .'/avatar/' . $_FILES['avatar']['name']);    

             /* Переименовываем имя файла в уникальное значение */
             $path = $_SERVER['DOCUMENT_ROOT'] . '/resource/users/' .  $_SESSION["user"]["id_user"] . '/avatar/';
             $fileNameModified = uniqid() . '.' . $extensionFile;
             $newFileName = rename($path . $_FILES['avatar']['name'], $path . $fileNameModified);

             /* Заносим в Базу Данных */

            $app_user = $db->prepare("UPDATE `users` SET `avatar` = :avatar WHERE `id_user` = :id_user");
            $app_user->execute([
                "avatar" => $fileNameModified,
                "id_user" => $_SESSION["user"]["id_user"],
            ]);
            $_SESSION["user"]["avatar"] = $fileNameModified;



        }else{
            $_SESSION['is_error'] = true;
            $_SESSION['error_message'] = 'Проверьте тип загружаемого файла';
            header('Location: /settings'); 
        }
    }



    header('Location: /settings');
?>



<!-- 
$app_user = $db->prepare("UPDATE `users` SET `avatar` = :avatar WHERE `id_user` = :id_user");
            $app_user->execute([
                "avatar" => 1,
                "id_user" => $_SESSION["user"]["id_user"],
            ]);
            $_SESSION["user"]["avatar"] = 1;

            $path = $_SERVER['DOCUMENT_ROOT'] . '/resource/users/' .  $_SESSION["user"]["id_user"] . '/avatar/*';
            array_map('unlink', glob($path));
            move_uploaded_file($_FILES['avatar']['tmp_name'], '../resource/users/'. $_SESSION["user"]["id_user"] .'/avatar/' .$_FILES['avatar']['name']); -->
