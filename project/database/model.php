<?php

abstract class Model
{
  public ?int $id = null;
  abstract public static function createTable(): void;
  abstract public function insertValue(): void;

}
