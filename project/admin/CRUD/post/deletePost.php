<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once ROOT . '/functions/auth.php';
include_once ROOT . '/database/post.php';

if (isset($_GET['id'])) {
  $id = strip_tags($_GET['id']);
  Post::deleteEntity($id);

  header("Location: /admin/index.php?status=del");
  die();
}
