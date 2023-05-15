<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once ROOT . '/database/user.php';
include_once ROOT . '/database/role.php';
include_once ROOT . '/database/image.php';
include_once ROOT . '/database/category.php';
include_once ROOT . '/database/comment.php';
include_once ROOT . '/database/post.php';
include_once ROOT . '/database/postCategory.php';

function dbPushTestRecords(): void
{
  $adminRole = new Role("Администратор");
  $adminRole->insertValue();

  $adminRole = new Role("Пользователь");
  $adminRole->insertValue();

  $user = new User(null,'Александр', 'Сергеевич', 'Усов', 'admin', '123', 'типа почта', 1);
  $user->insertValue();

  $user = new User(null,'User', 'User', 'User', 'user', '123', 'типа почта2');
  $user->insertValue();

  $defaultCategory = new Category("Без категории");
  $defaultCategory->insertValue();

  $authorId = 1;
  $posts = [
    [
      'title' => "First title 1",
      'text' => "First text 1"
    ],
    [
      'title' => "First title 2",
      'text' => "First text 2"
    ],
    [
      'title' => "First title 3",
      'text' => "First text 3"
    ],
    [
      'title' => "First title 4",
      'text' => "First text 4"
    ],
    [
      'title' => "First title 5",
      'text' => "First text 5"
    ],
    [
      'title' => "First title 6",
      'text' => "First text 6"
    ],
    [
      'title' => "First title 7",
      'text' => "First text 7"
    ],
    [
      'title' => "First title 8",
      'text' => "First text 8"
    ],
    [
      'title' => "First title 9",
      'text' => "First text 9"
    ],
    [
      'title' => "First title 10",
      'text' => "First text 10"
    ]
  ];
  foreach ($posts as $post) {
    $title = $post['title'];
    $text = $post['text'];
    $post = new Post($title, $text, $authorId);
    $post->insertValue();
  }

  //Добавить категорию первым 10 статьям
  setCategories();

  $comment = new Comment('Вау, это первый комментарий', 1, 1);
  $comment->insertValue();
}

function setCategories()
{
  for ($i = 1; $i <= 10; $i++) {
    $postCategory = new PostCategory($i, 1);
    $postCategory->insertValue();
  }
}
