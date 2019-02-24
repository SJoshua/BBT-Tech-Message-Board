<?php
require_once("../config.php");

session_start();

$ret = new StdClass();

if (isset($_SESSION["user_id"])) {
	$ret->errmsg = "You have to log out first.";
	
} else {
	if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["checkpwd"])) {
		$username = $_POST["username"];
		$pwd = $_POST["password"];
		$chk = $_POST["checkpwd"];
		
		if (strlen($username) > 20) {
			$ret->errmsg = "Your username is too long.";
		} elseif (strlen($username) < 3) {
			$ret->errmsg = "Your username is too short.";
		} elseif (preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $username)) {
			$ret->errmsg = "It's not allowed to use special characters in username.";
		} else {
			$con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
			if ($con->connect_error) {
				$ret->errmsg("Failed to access database: " . $con->connect_error);
			}

			$res = $con->query("select * from user_info where username='$username'");
			
			if ($res->num_rows) {
				$ret->errmsg = "The username has already been taken.";
			} elseif ($pwd != $chk) {
				$ret->errmsg = "Your password and confirmation password do not match.";
			} elseif (strlen($pwd) < 6) {
				$ret->errmsg = "Your password is too short.";
			} elseif (strlen($pwd) > 20) {
				$ret->errmsg = "Your password is too long.";
			} else {
				$enc_pwd = md5($pwd);
				$con->query("insert into user_info(username, password) values('$username', '$enc_pwd')");
			}
			
			$con->close();
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
