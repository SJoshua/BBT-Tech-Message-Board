<?php
require_once("../config.php");

session_start();

$ret = new StdClass();

if (isset($_SESSION["user_id"])) {
	$ret->errmsg = "You have to log out first.";
	$ret->status = "failed";

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
			$stmt = $con->prepare("select * from user_info where username=? and password=?");
			
			$stmt->bind_param("ss", $username, $enc_pwd);

			if ($stmt->execute()) {
				if (!$stmt->fetch()) {
					$ret->errmsg = "Either this account doesn't exist or the password is incorrect.";
				}
			}
			$stmt->close();
			$con->close();
		}
	} else {
		$ret->errmsg = "Incomplete information.";
	}

	if (!isset($ret->errmsg)) {
		$_SESSION["user_id"] = $username;
		if ($username == "admin") {
			$_SESSION["is_admin"] = true;
		}
		$ret->status = "ok";
	} else {
		$ret->status = "failed";
	}
}

echo json_encode($ret);
