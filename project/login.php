<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
session_start();

$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body>
<?php if (!empty($flash)): ?>
  <p style="color: red"><?= $flash ?></p>
<?php endif; ?>
<form class="loginForm" method="post" action="/functions/auth.php">
  <label for="loginField">Логин</label>
  <input class="loginFormElements" type="text" name="login" id="loginField">
  <label for="passwordField">Пароль</label>
  <input class="loginFormElements" type="password" name="password" id="passwordField">
  <input class="loginFormElements" type="submit">
</form>
</body>
</html>
