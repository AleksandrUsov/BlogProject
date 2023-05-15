<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once ROOT . '/database/connection.php';
require_once ROOT . '/database/model.php';

class Role extends Model
{
  public string $roleName;

  public function __construct(string $roleName)
  {
    $this->roleName = $roleName;
  }

  public static function createTable(): void
  {
    $queryStr = "
CREATE TABLE IF NOT EXISTS roles (
	id serial NOT NULL,
	role_name varchar(255) NOT NULL UNIQUE,
	CONSTRAINT PK_Roles PRIMARY KEY (id));";

    getConnection()->exec($queryStr);
  }

  public function insertValue(): void
  {
      $query = "
INSERT INTO roles(role_name) 
VALUES (:roleName);";

      $roleName = $this->roleName;

      $statement = getConnection()->prepare($query);
      $statement->execute(['roleName' => $roleName]);
  }
}
