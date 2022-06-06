<?php

    require_once 'vendor/db.php';
    session_start();


    if(!$_SESSION['auth'] && $_COOKIE['user'] ) {
        $user = $db->prepare("SELECT * FROM `users` WHERE `password` = :password");
        $user->execute([
        "password" => $_COOKIE['user'],
    ]);
        $user = $user->fetch();
        $_SESSION['auth'] = true;
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
    }
    
    if ($_SERVER['REQUEST_URI'] == '/') {
        $Page = 'index';
        $Module = 'index';
    } else {
        $URL_Path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $URL_Parts = explode('/', trim($URL_Path, '/'));
        $Page = array_shift($URL_Parts);
        $Module = array_shift($URL_Parts);

        if(!empty($Module)) {
            $Param = array();
            for($i = 0; $i < count($URL_Parts); $i++) {
                $Param[$URL_Parts[$i]] = $URL_Parts[++$i];
            }
        }
    }

    switch ($Page) {
        case 'index':
            include('pages/index.php');
            break;
        case 'main':
            include('pages/main.php');
            break;
        case 'profile':
            include('pages/profile.php');
            break;
        case 'friends': 
            include('pages/friends.php');
            break;
        case 'messages': 
            include('pages/chat.php');
            break;
        case 'users': 
            include('pages/users.php');
            break;
        case 'settings': 
            include('pages/settings.php');
            break;
        case 'admin': 
            include('pages/admin.php');
            break;
        case 'help': 
            include('pages/help.php');
            break;
        case $Page: 
            include('pages/user.php');
            break;
    } 
    /*
     if(include('pages/'.$Page.'.php'));
    else echo "404"
     */
?>
