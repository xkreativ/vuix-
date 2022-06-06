<?php

    require_once 'db.php';
    session_start();

    $image = $_POST['image'];
    $text = $_POST['text'];

    /* Проверяем существует ли данный файл */
    if($_FILES['image']['tmp_name']) {
            /* Проверяем размер файла */
        if ($_FILES['image']['size'] > 10 * 1024 * 1024) { 
            $_SESSION['is_error'] = true;
            $_SESSION['error_message'] = 'Размер файла превышает 10МБ';
            header('Location: /profile');

            /* Проверяем тип файла - если изображение - всё ОК */ 
        }elseif ($_FILES['image']['type'] == 'image/jpeg') {

                /* Определяем количество файлов в папке с постами */

                $dir = scandir($_SERVER['DOCUMENT_ROOT'] . '/resource/users/' .  $_SESSION["user"]["id_user"] . '/posts/');
                unset($dir[0], $dir[1]);

                /* Получаем расширение файла */

                $extensionFile = end(explode(".", $_FILES['image']['name']));

                /* Помещаем файл в папку */
                move_uploaded_file($_FILES['image']['tmp_name'], '../resource/users/'. $_SESSION["user"]["id_user"] .'/posts/' . $_FILES['image']['name']);    

                /* Переименовываем имя файла в уникальное значение */
                $path = $_SERVER['DOCUMENT_ROOT'] . '/resource/users/' .  $_SESSION["user"]["id_user"] . '/posts/';
                $fileNameModified = uniqid() . '.' . $extensionFile;
                $newFileName = rename($path . $_FILES['image']['name'], $path . $fileNameModified);

                /* Заносим в Базу Данных */

                $app_user = $db->prepare("INSERT INTO `posts`(`id_user`, `text_post`, `photo`, `created_at`) VALUES (:id_user, :text, :photo , :created_at)");
                $app_user->execute([
                    "id_user" => $_SESSION["user"]["id_user"],
                    "text" => $text,
                    "photo" => $fileNameModified,
                    "created_at" => date("Y-m-d H:i:s"),
                ]);

        }else{
            $_SESSION['is_error'] = true;
            $_SESSION['error_message'] = 'Проверьте тип загружаемого файла';
            header('Location: /profile'); 
        }


    } else {
        $_SESSION['is_error'] = true;
            $_SESSION['error_message'] = 'Изображение не выбрано';
            header('Location: /profile');  
    }

    header('Location: /profile');
?>