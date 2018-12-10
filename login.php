<?php
require_once("config.php");

session_start();

if (isset($_SESSION["user_id"])) {
	header('Location: index.php');
}

if (isset($_POST["username"]) && isset($_POST["password"])) {
	$username = $_POST["username"];
	$pwd = $_POST["password"];

	if (preg_match("/[\'.,:;*?~`!@#$%^&+=)(<>{}]|\]|\[|\/|\\\|\"|\|/", $username)) {
		$errmsg = "It's not allowed to use special characters in username.";
	} else {
		$enc_pwd = md5($pwd);
		$con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		if ($con->connect_error) {
			die("Failed to access database: " . $con->connect_error);
		}
		$res = $con->query("select * from user_info where username='$username' and password='$enc_pwd'");
		$con->close();
		if (empty($res->num_rows)) {
			$errmsg = "Either this account doesn't exist or the password is incorrect.";
		}
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
		<form action="./login.php" method="post">
			<h1> Message Board - Login </h1>
			<?php
			if (isset($errmsg)) {
				echo ('<div>' . $errmsg . '</div>');
			}
			?>
			<label for="username"> Username </label>
			<input id="username" name="username" placeholder="Username" required autofocus>
			<label for="password"> Password </label>
			<input id="password" type="password" name="password" placeholder="Password" required>
			<button type="submit"> Login </button>
		</form>
	</body>
</html>
