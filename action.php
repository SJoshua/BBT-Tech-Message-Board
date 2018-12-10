<?php
require_once("config.php");

session_start();

if (!isset($_SESSION["user_id"])) {
	header('Location: login.php');
} else {
	$con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	if ($con->connect_error) {
		die("Failed to access database: " . $con->connect_error);
	}
	
	$username = $_SESSION["user_id"];
	
	if ($_GET["thread"] == "submit") {
		if (!isset($_POST["message"])) {
			$errmsg = "No message.";
		} else {			
			$content = htmlspecialchars($_POST["message"], ENT_QUOTES);
			
			$stmt = $con->prepare("insert into messages(username, content) values(?, ?)");
			$stmt->bind_param("ss", $username, $content);
			$stmt->execute();
			$stmt->close();
		}
		
	} elseif ($_GET["thread"] == "edit") {
		if (!isset($_GET["id"])) {
			$errmsg = "No message id specified.";
		} else if (!isset($_POST["message"])) {
			$errmsg = "No message.";
		} else {
			$id = $_GET["id"];
			$res = $con->query("select * from messages where username='$username' and id='$id'");
			
			if (empty($res->num_rows)) {
				$errmsg = "Not found.";
			} else {
				$content = htmlspecialchars($_POST["message"], ENT_QUOTES);
				
				$stmt = $con->prepare("update messages set content = ? where id = ?");
				$stmt->bind_param("ss", $content, $id);
				$stmt->execute();
				$stmt->close();
			}
		}
		
	} elseif ($_GET["thread"] == "delete") {
		if (!isset($_GET["id"])) {
			$errmsg = "No message id specified.";
		} else {
			$id = $_GET["id"];
			$res = $con->query("select * from messages where username='$username' and id='$id'");
			
			if (empty($res->num_rows)) {
				$errmsg = "Not found.";
			} else {
				$con->query("delete from messages where id='$id'");
			}
		}
		
	} else {
		$errmsg = "Thread not specified.";
	}
	
	$con->close();
	
	if (isset($errmsg)) {
		die($errmsg);
	}
	
	header('Location: index.php');
}