<?php
include_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
include_once ROOT . "/database/createTables.php";
include_once ROOT . "/database/dbPushTestRecord.php";

createTables();
dbPushTestRecords();
