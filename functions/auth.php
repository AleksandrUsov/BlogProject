<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once ROOT . '/database/connection.php';
include_once ROOT . '/database/user.php';

session_start();

if (isset($_POST['login']) && isset($_POST['password'])) {
  $login = trim(htmlspecialchars(strip_tags($_POST['login'])));
  $password = trim(htmlspecialchars(strip_tags($_POST['password'])));

  if (auth($login, $password)) {
    header("Location: /");
  } else {
    header("Location: /login.php?flash");
    $_SESSION['flash'] = "Неверный логин или пароль";
  }
  die();
}

function auth(string $login, string $password): bool
{
  $user = User::getByLogin($login);

  if (!$user) return false;

  if (password_verify($password, $user->password)) {
    $_SESSION['auth'] = true;
    $_SESSION['id'] = $user->id;
    $_SESSION['role'] = $user->roleId;

    $authKey = getAuthKey();
    $user->setAuthKey($authKey);
    setcookie('authKey', $authKey, time() + 3600, '/');

    return true;
  } else {
    return false;
  }
}

if (empty($_SESSION['auth'])) {
  if (empty($_COOKIE['authKey'])) {
    header("Location: /login.php");
  } else {
    $user = User::findAuthKey($_COOKIE['authKey']);
    if ($user->authKey === $_COOKIE['authKey']) {
      $_SESSION['auth'] = true;
      $_SESSION['id'] = $user->id;
      $_SESSION['role'] = $user->roleId;
    }
  }
}

if (isset($_GET['logout'])) {
  setcookie('authKey', '', time(), '/');
  unset($_SESSION['auth']);
  header("Location: /");
  die();
}

function getAuthKey(): string
{
  $randValue = time() . mt_rand(1, 100);
  return password_hash($randValue, PASSWORD_DEFAULT);
}
