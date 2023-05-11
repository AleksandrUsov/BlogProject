<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once ROOT . '/database/connection.php';
require_once ROOT . '/database/model.php';

class Image extends Model
{
  public int $postId;
  public string $image;

  public function __construct(int $postId, string $image = 'image')
  {
    $this->postId = $postId;
    $this->image = $image;
  }

  public static function createTable(): void
  {
    $queryStr = "
    CREATE TABLE IF NOT EXISTS images (
    id serial NOT NULL,
	  post_id integer NOT NULL,
	  image varchar(255) NOT NULL,
	  CONSTRAINT PK_Images PRIMARY KEY (id),
	  CONSTRAINT FK_Images_Posts FOREIGN KEY (post_id) REFERENCES posts(id)
	      on update cascade on delete cascade);";

    getConnection()->exec($queryStr);
  }

  public function insertValue(): void
  {
    $query = "
    INSERT INTO images(post_id, image)VALUES 
        (:postId, :image);
    ";

    $statement = getConnection()->prepare($query);
    $statement->execute(['postId' => $this->postId, 'image' => $this->image]);
  }

  public static function getPostImages(int $postId): array | false
  {
    $query = "
    SELECT * FROM images
    WHERE post_id = :postId
    ";

    $statement = getConnection()->prepare($query);
    $statement->execute(['postId' => $postId]);
    return $statement->fetchAll();
  }

  public static function deleteEntity(int $id): void
  {
    $query = "
    DELETE FROM images
    WHERE id = :id
    ";

    $statement = getConnection()->prepare($query);
    $statement->execute(['id' => $id,]);
  }
}
