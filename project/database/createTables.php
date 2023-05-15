<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once ROOT . '/database/user.php';
include_once ROOT . '/database/role.php';
include_once ROOT . '/database/image.php';
include_once ROOT . '/database/category.php';
include_once ROOT . '/database/comment.php';
include_once ROOT . '/database/post.php';
include_once ROOT . '/database/postCategory.php';

function createTables(): void
{
  Role::createTable();
  User::createTable();
  Category::createTable();
  Post::createTable();
  PostCategory::createTable();
  Image::createTable();
  Comment::createTable();
}
