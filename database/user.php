<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_once ROOT . '/database/connection.php';
require_once ROOT . '/database/model.php';

class User extends Model
{
  public string $firstname;
  public ?string $patronymic;
  public string $surname;
  public string $login;
  public string $password;
  public string $email;
  public ?string $authKey;
  public int $roleId;


  public function __construct(?int   $id, string $firstname, string $patronymic, string $surname, string $login,
                              string $password, string $email, int $roleId = 2, ?string $authKey = null)
  {
    $this->id = $id;
    $this->firstname = $firstname;
    $this->patronymic = $patronymic;
    $this->surname = $surname;
    $this->login = $login;
    $this->email = $email;
    $this->authKey = $authKey;
    $this->roleId = $roleId;
    $this->password = $password;
  }


  public static function createTable(): void
  {
    $queryStr = "
CREATE TABLE IF NOT EXISTS users (
    id serial NOT NULL,
    firstname varchar(255) NOT NULL,
    patronymic varchar(255),
	  surname varchar(255) NOT NULL,
	  login varchar(255) NOT NULL UNIQUE,
	  password varchar(255) NOT NULL,
	  email varchar(255) NOT NULL UNIQUE,
	  auth_key varchar(255) UNIQUE,
	  role_id int NOT null default 1,
	  CONSTRAINT PK_Users PRIMARY KEY (id),
	  CONSTRAINT FK_Users_Roles FOREIGN KEY (role_id) REFERENCES roles(id)
	      on update cascade on delete set default);";

    getConnection()->exec($queryStr);
  }

  public function insertValue(): void
  {
    $query = "
INSERT INTO users(firstname, patronymic, surname, login, password, email, role_id) 
VALUES 
    (:firstname, :patronymic, :surname, :login, :password, :email, :roleId);";

    $firstname = $this->firstname;
    $patronymic = $this->patronymic;
    $surname = $this->surname;
    $login = $this->login;
    $password = $this->password;
    $email = $this->email;
    $roleId = $this->roleId;

    $statement = getConnection()->prepare($query);
    $statement->execute(['firstname' => $firstname, 'patronymic' => $patronymic,
      'surname' => $surname, 'login' => $login, 'password' => password_hash($password, PASSWORD_BCRYPT),
      'email' => $email, 'roleId' => $roleId]);
  }

  public function setAuthKey(string $authKey): void
  {
    $query = "
    UPDATE users
    SET auth_key = :authKey
    WHERE id = :id
    ";

    $statement = getConnection()->prepare($query);
    $statement->execute(['id' => $this->id, 'authKey' => $authKey]);
  }

  public static function getById(int $id): User|false
  {
    $query = "
    SELECT * FROM users
    WHERE id = :id
    ";

    $statement = getConnection()->prepare($query);
    $statement->execute(['id' => $id]);
    $result = $statement->fetch();

    if ($result === false) return false;

    return new User($result['id'], $result['firstname'], $result['patronymic'],
      $result['surname'], $result['login'], $result['password'],
      $result['email'], $result['role_id'], $result['auth_key']);
  }

  public static function getByLogin(string $login): User|false
  {
    $query = "
    SELECT * FROM users
    WHERE login = :login
    ";

    $statement = getConnection()->prepare($query);
    $statement->execute(['login' => $login]);
    $result = $statement->fetch();

    if ($result === false) return false;

    return new User($result['id'], $result['firstname'], $result['patronymic'],
      $result['surname'], $result['login'], $result['password'],
      $result['email'], $result['role_id'], $result['auth_key']);
  }

  public static function findAuthKey(string $authKey): User | false
  {
    $query = "
    SELECT * FROM users
    WHERE auth_key = :authKey
    ";

    $statement = getConnection()->prepare($query);
    $statement->execute(['authKey' => $authKey]);
    $result = $statement->fetch();

    if ($result === false) return false;

    return new User($result['id'], $result['firstname'], $result['patronymic'],
      $result['surname'], $result['login'], $result['password'],
      $result['email'], $result['role_id'], $result['auth_key']);
  }
}
