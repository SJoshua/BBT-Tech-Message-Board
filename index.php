<?php
require_once("config.php");

session_start();

$con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
if ($con->connect_error) {
	die("Failed to access database: " . $con->connect_error);
}
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<title> Message Board </title>
		<link type="text/css" rel="stylesheet" href="./css/style.css">
	</head>
	<body>
		<?php
		if (isset($_SESSION["user_id"])) {
			echo '<h1> Hello, ' . $_SESSION["user_id"] . '! </h1> Want to <a href="logout.php">logout</a>?<br>';
		} else {
			echo '<h1> Hello, guest. </h1>Please <a href="login.php">login</a> or <a href="signup.php">sign-up</a> to leave your message.<br>';
		}
		if (isset($_GET["thread"]) && $_GET["thread"] == "edit" && isset($_GET["id"]) && isset($_SESSION["user_id"])) {
			$username = $_SESSION["user_id"];
			$id = $_GET["id"];
			$res = $con->query("select * from messages where username='$username' and id='$id'");
			if (empty($res->num_rows)) {
				echo "<p><b>Target message not found.</b></p>";
			} else {
				$content = ($res -> fetch_assoc())["content"];
				print <<<EOT
				<form action="./action.php?thread=edit&id=$id" method="post">
					<h2> Edit your message #$id </h2>
					<textarea rows="10" cols="80" id="message" name="message" required>$content</textarea>
					<br>
					<button type="submit"> Submit </button>
				</form>
EOT;
			}			
		} else {
			if (isset($_SESSION["user_id"])) {
				print <<<EOT
			<form action="./action.php?thread=submit" method="post">
				<h2> Leave your message </h2>
				<textarea rows="10" cols="80" id="message" name="message" required>Your message</textarea>
				<br>
				<button type="submit"> Submit </button>
			</form>
EOT;
			}
			$page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;
			echo "<h2>Page " . $page . "</h2><br>";
			$cur = ($page - 1) * 10;
			$nxt = $page * 10;
			$nxt_p = $page + 1;
			$res = $con->query("select * from messages order by id desc limit $cur, 10");
			if (empty($res->num_rows) && $page > 1) {
				header('Location: index.php');
			}
			while ($row = $res -> fetch_assoc()) {
				echo "<p>#" . $row["id"] . " by <b>" . $row["username"] . "</b> at <i>" . $row["timestamp"] . "</i>";
				if ($row["username"] === $_SESSION["user_id"]) {
					echo ' [<a href="index.php?thread=edit&id=' . $row["id"] . '">EDIT</a>] [<a href="action.php?thread=delete&id=' . $row["id"] . '">DELETE</a>]';
				}
				echo "</p>";
				echo "<pre>" . $row["content"] . "</pre><br>";
			}
			
			$res = $con->query("select * from messages order by id desc limit $nxt, 10");
			$row = $res -> fetch_assoc();
			if (!empty($res->num_rows)) {
				echo '<p>[<a href="index.php?page=' . $nxt_p . '">PREV</a>]</p>';
			}
		}
		?>
	</body>
</html>