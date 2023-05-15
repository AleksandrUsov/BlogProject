<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once ROOT . '/functions/auth.php';
include_once ROOT . '/database/category.php';

if (isset($_GET['id'])) {
  $id = strip_tags($_GET['id']);
  Category::deleteEntity($id);

  header("Location: /admin/categories.php?status=del");
  die();
}
