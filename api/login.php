<?php
require_once("../config.php");

session_start();

$ret = new StdClass();

if (isset($_SESSION["user_id"])) {
	$ret->errmsg = "You have to log out first.";

} else {
	if (isset($_POST["username"]) && isset($_POST["password"])) {
		$username = $_POST["username"];
		$pwd = $_POST["password"];

		if (preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $username)) {
			$ret->errmsg = "It's not allowed to use special characters in username.";
		} else {
			$enc_pwd = md5($pwd);
			$con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			if ($con->connect_error) {
				die("Failed to access database: " . $con->connect_error);
			}
			$res = $con->query("select * from user_info where username='$username' and password='$enc_pwd'");
			$con->close();
			if (empty($res->num_rows)) {
				$ret->errmsg = "Either this account doesn't exist or the password is incorrect.";
			}
		}
	} else {
		$ret->errmsg = "Incomplete information.";
	}

	if (!isset($ret->errmsg)) {
		$_SESSION["user_id"] = $username;
		$ret->status = "ok";
	} else {
		$ret->status = "failed";
	}
}

echo json_encode($ret);