<?php 

    session_start();
    if(isset($_SESSION["auth"]) && $_SESSION["auth"] === true) {
        header('Location: /profile');
    }

?>




<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../css/style.css"/>
    <title>Регистрация & Авторизация</title>
  </head>
  <body>
    <div class="auth-wrapper <?= $_COOKIE['auth-toggle'] == 1 ? 'sign-up-mode' : '' ?>">
      <div class="forms-container">
        <div class="signin-signup">
          <form action="vendor/auth.php" method="POST"  class="sign-in-form auth-form" id="form-login">
            <h2 class="title">Вход</h2>
            <div class="input-field">
              <ion-icon name="person-circle-outline"></ion-icon>
              <input type="email" name="email" placeholder="Введите почту" />
            </div>
            <div class="input-field">
              <ion-icon name="lock-open-outline"></ion-icon>
              <input type="password" name="password" placeholder="Ваш пароль" />
            </div>
            <input name="submit-login" type="submit" value="Войти" class="btn solid" />
            <?php if($_COOKIE['auth-toggle'] == 0) { ?>
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
                <?php if(isset($_SESSION['is_success_register']) && $_SESSION['is_success_register'] === true){
                    ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['success_message'] ?>
                    </div>
                <?php
                }
                unset($_SESSION['is_success']);
                unset($_SESSION['success_message']);
                ?> <?
            } ?>
          </form>
          <form action="vendor/auth.php" method="POST" class="sign-up-form auth-form" id="form-register">
            <h3 class="title">Создание аккаунта</h3>
            <!-- <div class="reg__with">
                <div class="reg__with_box"><ion-icon name="logo-google"></ion-icon> Sign with Google</div>
                <div class="reg__with_box"><ion-icon name="logo-facebook"></ion-icon>Sign with Facebook</div>
            </div>
            <div class="reg__alt"> <span>или</span></div>
     -->
            <div class="inp__wrapper">
                <div class="inp__row">
                    <ion-icon name="person-outline"></ion-icon>
                    <input type="text" name="name" placeholder="Ваше имя">
                    <input type="text" name="surname" placeholder="Ваша фамилия">
                </div>
                <div class="inp__row">
                    <ion-icon name="ticket-outline"></ion-icon>
                    <input type="text" name="login" placeholder="Придумайте логин">
                </div>
                <div class="inp__row">
                    <ion-icon name="mail-outline"></ion-icon>
                    <input type="email" name="email" placeholder="Ваш Email">
                </div>
                <div class="inp__row">
                    <ion-icon name="finger-print-outline"></ion-icon>
                    <input type="password" name="password" placeholder="Придумайте пароль">
                    <input type="password" name="password_confirm" placeholder="Повторите пароль">
                </div>
            </div>
            <div class="reg__btn_box">
                <input name="submit-register" value="Создать" type="submit" class="btn">
            </div>
            <?php if($_COOKIE['auth-toggle'] == 1) { ?>
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
                <?php if(isset($_SESSION['is_success_register']) && $_SESSION['is_success_register'] === true){
                    ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['success_message'] ?>
                    </div>
                <?php
                }
                unset($_SESSION['is_success']);
                unset($_SESSION['success_message']);
                ?> <?
            } ?>
          </form>
        </div>
      </div>

      <div class="panels-container">
        <div class="panel left-panel">
          <div class="content">
            <h3>Новичок?</h3>
            <p>
              У нас море контента, будь одним из нас, и тебе понравится
            </p>
            <button class="btn transparent" id="sign-up-btn">
              Зарегистрироваться
            </button>
          </div>
          <img src="../img/log.svg" class="image" alt="" />
        </div>
        <div class="panel right-panel">
          <div class="content">
            <h3>Уже зарегистрирован?</h3>
            <p>
              Чего же ты ждёшь, скорее заходи, твои друзья уже соскучились!
            </p>
            <button class="btn transparent" id="sign-in-btn">
              Войти
            </button>
          </div>
          <img src="../img/register.svg" class="image" alt="" />
        </div>
      </div>
    </div>

    <script src="../js/auth.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script> 
  </body>
</html>
