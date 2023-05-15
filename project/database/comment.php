<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once ROOT . '/database/connection.php';
require_once ROOT . '/database/model.php';

class Comment extends Model
{
  public string $commentText;
  public int $postId;
  public int $commentAuthorId;

  public function __construct(string $commentText, int $postId, int $commentAuthorId)
  {
    $this->commentText = $commentText;
    $this->postId = $postId;
    $this->commentAuthorId = $commentAuthorId;
  }

  public static function createTable(): void
  {
    $queryStr = "
CREATE TABLE IF NOT EXISTS comments (
    id serial NOT NULL,
    comment_text varchar(255) NOT NULL,
    post_id int NOT NULL,
    comment_author_id int NOT NULL,
    CONSTRAINT PK_Comments PRIMARY KEY (id),
    CONSTRAINT FK_Comments_Posts FOREIGN KEY (post_id) REFERENCES posts(id)
        on update cascade on delete cascade,
    CONSTRAINT FK_Comments_Users FOREIGN KEY (comment_author_id) REFERENCES users(id)
        on update cascade on delete cascade);";

    getConnection()->exec($queryStr);
  }

  public function insertValue(): void
  {
    $query = "
INSERT INTO comments(comment_text, post_id, comment_author_id) 
VALUES (:comment_text, :post_id, :comment_author_id);";

    $text = $this->commentText;
    $postId = $this->postId;
    $commentAuthorId = $this->commentAuthorId;

    $statement = getConnection()->prepare($query);
    $statement->execute(['comment_text' => $text, 'post_id' => $postId,
      'comment_author_id' => $commentAuthorId]);
  }
}
