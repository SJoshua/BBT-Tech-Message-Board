<?php
require_once("config.php");

session_start();

if (isset($_SESSION["user_id"])) {
	header('Location: index.php');
}

if (isset($_POST["username"]) && isset($_POST["password"]) && isset($_POST["checkpwd"])) {
	$username = $_POST["username"];
	$pwd = $_POST["password"];
	$chk = $_POST["checkpwd"];
	
	if (strlen($username) > 20) {
		$errmsg = "Your username is too long.";
	} elseif (strlen($username) < 3) {
		$errmsg = "Your username is too short.";
	} elseif (preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $username)) {
		$errmsg = "It's not allowed to use special characters in username.";
	} else {
		$con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		if ($con->connect_error) {
			die("Failed to access database: " . $con->connect_error);
		}

		$res = $con->query("select * from user_info where username='$username'");
		
		if ($res->num_rows) {
			$errmsg = "The username has already been taken.";
		} elseif ($pwd != $chk) {
			$errmsg = "Your password and confirmation password do not match.";
		} elseif (strlen($pwd) < 6) {
			$errmsg = "Your password is too short.";
		} elseif (strlen($pwd) > 20) {
			$errmsg = "Your password is too long.";
		} else {
			$enc_pwd = md5($pwd);
			$con->query("insert into user_info(username, password) values('$username', '$enc_pwd')");
		}
		
		$con->close();
	}
	
	if (!isset($errmsg)) {
		$_SESSION["user_id"] = $username;
		header('Location: index.php');
	}
}
?>

<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title> Message Board - Login </title>
		<link type="text/css" rel="stylesheet" href="./css/style.css">
	</head>
	<body>
		<form action="./signup.php" method="post">
			<h1> Message Board - Sign up </h1>
			<?php
			if (isset($errmsg)) {
				echo ('<div>' . $errmsg . '</div>');
			}
			?>
			<label for="username"> Username </label>
			<input id="username" name="username" placeholder="Username" required autofocus>
			<label for="password"> Password </label>
			<input id="password" type="password" name="password" placeholder="Password" required>
			<label for="checkpwd"> Confirm Password </label>
			<input id="checkpwd" type="password" name="checkpwd" placeholder="Confirm Password" required>
			<button type="submit"> Sign up </button>
		</form>
	</body>
</html>
