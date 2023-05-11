<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once ROOT . '/functions/auth.php';
include ROOT . '/database/post.php';
include ROOT . '/database/category.php';
include ROOT . '/database/image.php';

$postId = $_GET['postId'];
$post = Post::getById($postId);
$postCategories = Category::getPostCategories($postId);
$images = Image::getPostImages($postId);

//print_r($images);
//die();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Пост</title>
  <link rel="stylesheet" type="text/css" href="/css/style.css">
</head>
<body>
<?php include ROOT . '/widgets/menu.php' ?>
<?php include_once ROOT . '/logout.php' ?>
<h2><?= $post->postTitle ?></h2>
<p><?= $post->postText ?></p>
<?php if ($images): ?>
  <div>
    <?php foreach ($images as $image): ?>
      <img class="inlineBlock" src="/images/<?= $image['image'] ?>" width="128" height="128">
    <?php endforeach; ?>
  </div>
<?php endif; ?>
<p style="color: gray">
  <?php foreach ($postCategories as $category): ?>
    <?= "[" . $category->categoryName . "]" ?>
  <?php endforeach; ?>
</p>
<p></p>
</body>
</html>
