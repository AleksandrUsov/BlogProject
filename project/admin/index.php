<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once ROOT . '/database/post.php';

//Read
$posts = Post::getAll();

const STATUS = [
  'add' => 'Пост создан',
  'update' => 'Пост изменён',
  'del' => 'Пост удалён'
  ];

$status = !empty($_GET['status']) ? STATUS[$_GET['status']] : '';

?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Document</title>
</head>
<body>
<?php include ROOT . "/widgets/admin.php" ?>
<h3 style="color: tomato"><?=$status?></h3>
<a href="/admin/CRUD/post/createPost.php">[Create]</a>
<?php foreach ($posts as $post): ?>
    <h3>
      <?=$post->postTitle?>
      <a href="/admin/CRUD/post/updatePost.php?id=<?=$post->id?>">[Edit]</a>
      <a href="/admin/CRUD/post/deletePost.php?id=<?=$post->id?>">[Delete]</a>
    </h3><hr>
<?php endforeach; ?>
</body>
</html>
