<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once ROOT . '/database/connection.php';
require_once ROOT . '/database/model.php';
require_once ROOT . '/database/postCategory.php';

class   Post extends Model
{
  public string $postTitle;
  public string $postText;
  public int $authorId;
  public int $categoryId;

  public function __construct(string $postTitle, string $postText, int $authorId, int $id = null)
  {
    $this->id = $id;
    $this->postTitle = $postTitle;
    $this->postText = $postText;
    $this->authorId = $authorId;
  }

  public static function createTable(): void
  {
    $queryStr = "
CREATE TABLE IF NOT EXISTS posts (
	id serial NOT NULL,
	post_title text NOT NULL,
	post_text text NOT NULL,
	author_id int NOT NULL,
	CONSTRAINT PK_Posts PRIMARY KEY (id),
	CONSTRAINT FK_Posts_Users FOREIGN KEY (author_id) REFERENCES users(id)
	    on update cascade on delete cascade
);";

    getConnection()->exec($queryStr);
  }

  public function insertValue(): void
  {

    $query = "
INSERT INTO posts(post_title, post_text, author_id) 
VALUES (:post_title, :post_text, :author_id);";

    $title = $this->postTitle;
    $text = $this->postText;
    $authorId = $this->authorId;

    $statement = getConnection()->prepare($query);
    $statement->execute(['post_title' => $title, 'post_text' => $text,
      'author_id' => $authorId]);
  }

  public static function getAll(): array
  {
    $query = "
SELECT * FROM posts
ORDER BY id DESC";

    $statement = getConnection()->query($query);
    $posts = $statement->fetchAll();

    return static::сonvertToPost($posts);
  }

  public static function getPostsWithCategory(int $categoryId, int $limit = 5): array
  {
    $query = "
SELECT id, post_title, post_text, author_id FROM posts p
INNER JOIN post_category pc on pc.post_id = p.id
WHERE category_id = :categoryId
ORDER BY id DESC 
LIMIT :limit
";
    $statement = getConnection()->prepare($query);
    $statement->execute(['categoryId' => $categoryId, 'limit' => $limit]);
    $posts = $statement->fetchAll();

    return static::сonvertToPost($posts);
  }

  private static function сonvertToPost(array $posts): array
  {
    $result = [];

    foreach ($posts as $post) {
      $postId = $post['id'];
      $postTitle = $post['post_title'];
      $postText = $post['post_text'];
      $postAuthor = $post['author_id'];
      $result[] = new Post($postTitle, $postText, $postAuthor, $postId);
    }
    return $result;
  }

  public static function getById(int $id): Post
  {
    $query = "
SELECT * FROM posts
WHERE id = :id";

    $statement = getConnection()->prepare($query);
    $statement->execute(['id' => $id]);
    $post = $statement->fetch();

    $postId = $post['id'];
    $postTitle = $post['post_title'];
    $postText = $post['post_text'];
    $postAuthor = $post['author_id'];

    return new Post($postTitle, $postText, $postAuthor, $postId);
  }

  public static function deleteEntity(int $id): void
  {
    $query = "
    DELETE FROM posts
    WHERE id = :id;
    ";

    $statement = getConnection()->prepare($query);
    $statement->execute(['id' => $id]);
  }

  public function updateEntity(): void
  {
    $query = "
    UPDATE posts
    SET post_title = :title,
        post_text = :text
    WHERE id = :id
    ";

    $statement = getConnection()->prepare($query);
    $statement->execute(['title' => $this->postTitle, 'text' => $this->postText, 'id' => $this->id]);
  }
}
