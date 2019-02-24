<?php
session_start();

$ret = new StdClass();

if (isset($_SESSION["user_id"])) {
	$ret->user_id = $_SESSION["user_id"];
	$ret->status = "user";
	if (isset($_SESSION["is_admin"])) {
		$ret->admin = true;
	}
} else {
	$ret->status = "guest";
}

echo json_encode($ret);
