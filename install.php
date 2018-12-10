<?php
require_once("config.php");

$con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($con->connect_error) {
	die("Failed to access database: " . $con->connect_error);
}

/* to-do: verify the result */

$con->query("CREATE TABLE `" . DB_NAME . "`.`user_info` ( `username` TEXT NOT NULL , `password` TEXT NOT NULL ) ENGINE = InnoDB;");
$con->query("CREATE TABLE `" . DB_NAME . "`.`messages` ( `id` INT NOT NULL AUTO_INCREMENT , `username` TEXT NOT NULL , `content` TEXT NOT NULL , `timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`id`)) ENGINE = InnoDB;");

$con->close();

print("done.");
