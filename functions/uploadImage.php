<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once ROOT . '/functions/auth.php';

function uploadImage(mixed $file): string | false
{

  if ($file['error']) {
    throwError('uploadError');
  }

  define('MAX_FILE_SIZE', 2097152);

  if ($file['size'] > MAX_FILE_SIZE) {
    throwError('maxSize');
  }

  $whiteList = ['png', 'jpeg', 'gif'];
  $fileType = basename($file['type']);

  if (!in_array($fileType, $whiteList)) {
    throwError('SupportedTypes');
  }

  $pathToSave = ROOT . '/images';
  if (!is_dir($pathToSave)) {
    mkdir($pathToSave);
  }

  $tempFilePath = $file['tmp_name'];
  $fileName = $file['name'];

  if (!move_uploaded_file($tempFilePath, $pathToSave . "/$fileName" )) {
    throwError('unknownError');
  }

  return $fileName;
}

function throwError(string $message): void
{
  header("Location: ?status=$message");
  die();
}

