<?php 

    session_start();
    require_once 'db.php';

    if ( ! empty($_POST)) {
        if (isset($_POST['submit-register'])) {
            $name = $_POST['name'];
            $surname = $_POST['surname'];
            $login = $_POST['login'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];
        
        
           
            /* Количество символов в логине */
            if(
                $name === '' ||
                $surname === '' || 
                $login === '' ||
                $email === '' ||
                $password === '' ||
                $password_confirm === '' ||
                !preg_match("/^[a-z0-9_]{4,20}$/i", $login) ||
                !preg_match("/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/", $email) ||
                !preg_match("/^[a-zА-Я]{2,20}$/iu", $name) || 
                !preg_match("/^[a-zА-Я]{3,20}$/iu", $surname)
            ) {
                $_SESSION['is_error'] = true;
                $_SESSION['error_message'] = 'Проверьте правильность полей при регистрации';
                header('Location: /');
            } else if($password !== $password_confirm) {
                $_SESSION['is_error'] = true;
                $_SESSION['error_message'] = 'Пароли не совпадают при регистрации';
                header('Location: /');
            } else if($user["email"] == $email) { 
                $_SESSION['is_error'] = true;
                $_SESSION['error_message'] = 'Пользователь с таким email уже существует при регистрации';
                header('Location: /'); 
            }else if($user["login"] == $login) { 
                $_SESSION['is_error'] = true;
                $_SESSION['error_message'] = 'Пользователь с таким Логином уже существует при регистрации';
                header('Location: /'); 
            } else {
                $app_user =  $db->prepare("INSERT INTO `users` (`name`, `surname`, `email`, `login`, `password`, `activity`, `info`, `link`, `avatar`) VALUES (:name, :surname, :email, :login, :password, `activity`, `info`, `link`, `avatar`)");
                $app_user->execute([
                "name" => $name,
                "surname" => $surname,
                "email" => $email,
                "login" => $login,
                "password" => password_hash($password, PASSWORD_DEFAULT),
                
            ]);
                $_SESSION['is_success_register'] = true;
                $_SESSION['success_message'] = 'Регистрация завершена! Авторизируйтесь';
                header('Location: /');
            }
        } else if (isset($_POST['submit-login'])) {
            $email = $_POST['email'];
    $password = $_POST['password'];

    if(
        $email === '' ||
        $password === '' ||
        !preg_match("/^([a-z0-9_-]+\.)*[a-z0-9_-]+@[a-z0-9_-]+(\.[a-z0-9_-]+)*\.[a-z]{2,6}$/", $email)
    ) {
        $_SESSION['is_error'] = true;
        $_SESSION['error_message'] = 'Проверьте правильность полей';
        header('Location: /');
    }

    $user = $db->prepare("SELECT * FROM `users` WHERE `email` = :email");
    $user->execute([
        "email" => $email
    ]);
    $user = $user->fetch();


    if(!$user) {
        $_SESSION['is_error'] = true;
        $_SESSION['error_message'] = 'Пользователь не найден';
        header('Location: /');
    } else if(password_verify($password, $user["password"]) === true) {
        $_SESSION["auth"] = true;
        $_SESSION["user"] = [
            "id_user" => $user["id_user"],
            "name" => $user["name"],
            "surname" => $user["surname"],
            "email" => $user["email"],
            "login" => $user["login"],
            "password" => $user["password"],
            "activity" => $user["activity"],
            "info" => $user["info"],
            "link" => $user["link"],
            "avatar" => $user["avatar"],
        ];

        /* Создаём папки для хранения изображений пользователя если они не существуют */
        
        $path = '../resource/users/';
        $folder = $_SESSION["user"]["id_user"];
        if(!is_dir($path . $folder)) {
            mkdir($path . $folder);
        }
        if(!is_dir($path . $folder . '/' . 'avatar') && is_dir($path . $folder)) {
            mkdir($path . $folder . '/' . 'avatar');
        }
        if(!is_dir($path . $folder . '/' . 'posts') && is_dir($path . $folder)) {
            mkdir($path . $folder . '/' . 'posts');
        }

        setcookie('user', $user["password"], time()+60*60*24*30, '/'); 
        setcookie ("auth-toggle", "", time() - 3600);
        exit(header('Location: /profile'));
    
    } else {
        $_SESSION['is_error'] = true;
        $_SESSION['error_message'] = 'Не верный пароль';
        header('Location: /');
    }
        } else {
            echo 'ошибка';
        }
    } 