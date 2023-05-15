<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once ROOT . '/functions/auth.php';
include_once ROOT . '/database/post.php';
include_once ROOT . '/database/category.php';
include_once ROOT . '/database/postCategory.php';
include_once ROOT . '/database/connection.php';
include_once ROOT . '/database/image.php';
include_once ROOT . '/functions/uploadImage.php';

$categories = Category::getAll();

const STATUS = [
  'uploadError' => 'Ошибка загрузки файла',
  'maxSize' => 'Максимальный размер файла 2 мб',
  'SupportedTypes' => 'Поддерживаемый тип изображений: png, jpeg, gif',
  'unknownError' => 'Что-то пошло не так',
  'update' => 'Пост изменён'
];

$status = !empty($_GET['status']) ? STATUS[$_GET['status']] : '';

//Create
if (isset($_POST['title']) && isset($_POST['category']) && isset($_POST['text'])) {
  $postTitle = trim(htmlspecialchars(strip_tags($_POST['title'])));
  $postText = trim(htmlspecialchars(strip_tags($_POST['text'])));
  $authorId = 1; //Заглушка

  $newPost = new Post($postTitle, $postText, $authorId);
  getConnection()->beginTransaction();
  $newPost->insertValue();

  $categoryList = $_POST['category'];

  $postId = getConnection()->lastInsertId();

  foreach ($categoryList as $category) {
    $postCategory = new PostCategory($postId, $category);
    $postCategory->insertValue();
  }
  getConnection()->commit();

  if (!empty($_FILES['image']['name'])) {

    $fileName = uploadImage($_FILES['image']);
    if ($fileName) {
      $image = new Image($postId,$fileName);
      $image->insertValue();
    } else {
      header("Location: ?status=uploadError");
      die();
    }
  }

  header("Location: /admin/index.php?status=add");
  die();
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link rel="stylesheet" type="text/css" href="/css/style.css">
  <title>Пост</title>
</head>
<body>
<?php include ROOT . "/widgets/admin.php" ?>
<?php include_once ROOT . '/logout.php' ?>
<?php if (!empty($status)): ?>
  <p style="color: red"><?= $status ?></p>
<?php endif; ?>
<form action="#" method="post" enctype="multipart/form-data">
  <div style="display: flex; flex-direction: column">
    <label for="postTitle">Заголовок</label>
    <input type="text" name="title" id="postTitle" style=" max-width: 500px;"><br>

    <label for="postCategory">Категория</label>
    <select name="category[]" id="postCategory" multiple style="max-width: 200px">
      <?php foreach ($categories as $category): ?>
        <option value="<?= $category->id ?>"><?= $category->categoryName ?></option>
      <?php endforeach; ?>
    </select><br>

    <label for="postText">Текст</label>
    <textarea name="text" cols="30" rows="10" id="postText"></textarea><br>
    <input type="file" name="image"><br>
  </div>
  <input type="submit" value="Создать">
</form>
</body>
</html>
