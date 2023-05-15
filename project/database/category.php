<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once ROOT . '/database/connection.php';
require_once ROOT . '/database/model.php';

class Category extends Model
{
  public string $categoryName;

  public function __construct(string $categoryName, int $id = null)
  {
    $this->id = $id;
    $this->categoryName = $categoryName;

  }

  public static function createTable(): void
  {
    $queryStr = "
CREATE TABLE IF NOT EXISTS categories (
	id serial NOT NULL,
	category_name varchar(255) NOT NULL UNIQUE,
	CONSTRAINT PK_Categories PRIMARY KEY (id));";

    getConnection()->exec($queryStr);
  }

  public function insertValue(): void
  {
    $query = "
INSERT INTO categories(category_name) 
VALUES (:categoryName);";

    $categoryName = $this->categoryName;

    $statement = getConnection()->prepare($query);
    $statement->execute(['categoryName' => $categoryName]);
  }

  public static function getAll(): array
  {
    $query = "
SELECT * FROM categories
ORDER BY id DESC;
";

    $statement = getConnection()->query($query);
    $categories = $statement->fetchAll();

    $result = [];

    foreach ($categories as $category) {
      $id = $category['id'];
      $categoryName = $category['category_name'];
      $element = new Category($categoryName, $id);
      $result[] = $element;
    }
    return $result;
  }

  public static function getById(int $id): Category
  {
    $query = "
SELECT * FROM categories
WHERE id = :id";

    $statement = getConnection()->prepare($query);
    $statement->execute(['id' => $id]);
    $category = $statement->fetch();

    $categoryId = $category['id'];
    $categoryName = $category['category_name'];

    return new Category($categoryName, $categoryId);
  }

  public static function getPostCategories(int $postId): array
  {
    $query = "
    SELECT * FROM categories c
    INNER JOIN post_category pc on pc.category_id = c.id
    WHERE post_id = :postId;
    ";

    $statement = getConnection()->prepare($query);
    $statement->execute(['postId' => $postId]);
    $categories = $statement->fetchAll();

    $result = [];
    foreach ($categories as $category) {
      $categoryId = $category['id'];
      $categoryName = $category['category_name'];
      $result[] = new Category($categoryName, $categoryId);
    }

    return $result;
  }

  public static function deleteEntity(int $id): void
  {
    $query = "
    DELETE FROM categories
    WHERE id = :id;
    ";

    $statement = getConnection()->prepare($query);
    $statement->execute(['id' => $id]);
  }

  public function updateEntity(): void
  {
    $query = "
    UPDATE categories
    SET category_name = :categoryName
    WHERE id = :id
    ";

    $statement = getConnection()->prepare($query);
    $statement->execute(['categoryName' => $this->categoryName, 'id' => $this->id]);
  }
}
