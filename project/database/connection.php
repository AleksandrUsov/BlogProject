<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';

function getConnection(): PDO
{
  static $pdo = null;

  if (empty($pdo)) {
    $dsn = "pgsql:host=localhost;dbname=asap";

    $pdo = new PDO($dsn, DB_USERNAME, DB_PASSWORD, DB_OPTIONS);
  }

  return $pdo;
}
