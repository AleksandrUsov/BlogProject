<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once ROOT . '/functions/auth.php';
include_once ROOT . '/database/post.php';
include_once ROOT . '/database/category.php';
include_once ROOT . '/database/image.php';
include_once ROOT . '/database/postCategory.php';
include_once ROOT . '/database/connection.php';
include_once ROOT . '/functions/uploadImage.php';

const STATUS = [
  'uploadError' => 'Ошибка загрузки файла',
  'maxSize' => 'Максимальный размер файла 2 мб',
  'SupportedTypes' => 'Поддерживаемый тип изображений: png, jpeg, gif',
  'unknownError' => 'Что-то пошло не так',
  'update' => 'Пост изменён'
];

$status = !empty($_GET['status']) ? STATUS[$_GET['status']] : '';

$raw = [
  'id' => '',
  'title' => '',
  'text' => '',
  'authorId' => 1,
  'categories' => null,
  'images' => null
];

if (isset($_GET['id'])) {
  $id = $_GET['id'];
  $post = Post::getById($id);

  $raw = setRawPost($post);
  $_SESSION['rawPost'] = $raw;
} else {
  $raw = $_SESSION['rawPost'];
}

if (isset($_GET['deleteImage'])) {
  $imageId = $_GET['deleteImage'];
  Image::deleteEntity($imageId);

  $id = $_SESSION['rawPost']['id'];
  $images = Image::getPostImages($id);
  $_SESSION['rawPost']['images'] = $images;
  $messageText = "Пост изменён";
  header("Location: ?status=update");
  die();
}

$categories = Category::getAll();

//EDIT
if (!empty($_POST)) {
  $id = $_POST['id'];
  $newTitle = trim(htmlspecialchars(strip_tags($_POST['title'])));
  $newText = trim(htmlspecialchars(strip_tags($_POST['text'])));
  $author = $_POST['author'];

  $newPost = new Post($newTitle, $newText, $author, $id);
  $_SESSION['rawPost'] = setRawPost($newPost);

  getConnection()->beginTransaction();
  $newPost->updateEntity();

  if (isset($_POST['category'])) {
    PostCategory::deleteCategoriesForPost($newPost->id);
    $newCategories = $_POST['category'];

    foreach ($newCategories as $category) {
      $postCategory = new PostCategory($id, $category);
      $postCategory->insertValue();
    }
  }

  if (!empty($_FILES['image']['name']) && is_null($_POST['deleteImage'])) {
    $fileName = uploadImage($_FILES['image']);

    $oldImage = Image::getPostImages($id);

    $postImage = new Image($id, $fileName);
    $postImage->insertValue();
  }

  getConnection()->commit();

  header("Location: ?id=$id&status=update");
  die();
}

function setRawPost(Post $post): array
{
  $postCategories = Category::getPostCategories($post->id);
  $postImage = Image::getPostImages($post->id);
  return [
    'id' => $post->id,
    'title' => $post->postTitle,
    'text' => $post->postText,
    'authorId' => $post->authorId,
    'categories' => $postCategories,
    'images' => $postImage
  ];
}

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
<?php include ROOT . "/widgets/admin.php" ?>
<?php include_once ROOT . '/logout.php' ?>
<?php if (!empty($status)): ?>
  <p style="color: red"><?= $status ?></p>
<?php endif; ?>
<form action="#" method="post" enctype="multipart/form-data">
  <div style="display: flex; flex-direction: column">
    <input type="text" name="id" hidden="hidden" value="<?= $raw['id'] ?>" style=" max-width: 500px;"><br>

    <label for="postTitle">Заголовок</label>
    <input type="text" name="title" id="postTitle" value="<?= $raw['title'] ?>" style=" max-width: 500px;"><br>

    <label for="postCategory">Категория</label>
    <select name="category[]" id="postCategory" multiple style="max-width: 200px">
      <?php foreach ($categories as $category): ?>
        <option value="<?= $category->id ?>"><?= $category->categoryName ?></option>
      <?php endforeach; ?>
    </select><br>

    <label for="postText">Текст</label>
    <textarea name="text" cols="30" rows="10" id="postText"><?= $raw['text'] ?></textarea>

    <input type="text" name="author" hidden="hidden" value="<?= $raw['authorId'] ?>" style=" max-width: 500px;">
  </div>
  <?php if ($raw['images']): ?>
    <?php foreach ($raw['images'] as $image): ?>
    <div class="inline-block">
      <img src="/images/<?= $image['image'] ?>" width="128" height="128">
      <a class="deleteIcon" href="?deleteImage=<?=$image['id']?>">X</a>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
  <p style="color: gray">
    <?php foreach ($raw['categories'] as $category): ?>
      <?= "[" . $category->categoryName . "]" ?>
    <?php endforeach; ?>
  </p><br>
  <input type="file" name="image"><br>
  <input type="submit" value="Изменить">
</form>
</body>
</html>
