<?php
require_once("../config.php");

session_start();

$ret = new StdClass();

if (!isset($_SESSION["user_id"])) {
	$ret->errmsg = "You have to log in first.";
	
} else {
	$con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

	if ($con->connect_error) {
		die("Failed to access database: " . $con->connect_error);
	}
	
	$username = $_SESSION["user_id"];
	
	if ($_GET["method"] == "submit") {
		if (!isset($_POST["message"])) {
			$ret->errmsg = "No message.";
		} else {			
			$content = htmlspecialchars($_POST["message"], ENT_QUOTES);
			
			$stmt = $con->prepare("insert into messages(username, content) values(?, ?)");
			$stmt->bind_param("ss", $username, $content);
			$stmt->execute();
			$stmt->close();
		}
		
	} elseif ($_GET["method"] == "reply") {
		if (!isset($_POST["message"])) {
			$ret->errmsg = "No message.";
		} elseif (!isset($_GET["post_id"])) {
			$ret->errmsg = "No post id specified.";
		} else {			
			$content = htmlspecialchars($_POST["message"], ENT_QUOTES);
			
			$stmt = $con->prepare("insert into comments(username, post_id, content) values(?, ?, ?)");
			$stmt->bind_param("sss", $username, $_GET["post_id"], $content);
			$stmt->execute();
			$stmt->close();
		}

	} elseif ($_GET["method"] == "edit") {
		if (!isset($_GET["id"])) {
			$ret->errmsg = "No message id specified.";
		} else if (!isset($_POST["message"])) {
			$ret->errmsg = "No message.";
		} else {
			$id = $_GET["id"];
			$res = $con->query("select * from messages where username='$username' and id='$id'");
			
			if (empty($res->num_rows)) {
				$ret->errmsg = "Not found.";
			} else {
				$content = htmlspecialchars($_POST["message"], ENT_QUOTES);
				
				$stmt = $con->prepare("update messages set content = ? where id = ?");
				$stmt->bind_param("ss", $content, $id);
				$stmt->execute();
				$stmt->close();
			}
		}
		
	} elseif ($_GET["method"] == "delete") {
		if (!isset($_GET["id"])) {
			$ret->errmsg = "No message id specified.";
		} else {
			$id = $_GET["id"];
			$res = $con->query("select * from messages where username='$username' and id='$id'");
			
			if (empty($res->num_rows)) {
				$ret->errmsg = "Not found.";
			} else {
				$con->query("delete from messages where id='$id'");
			}
		}

	} else {
		$ret->errmsg = "Method not specified.";
	}
	
	$con->close();
}

$ret->status = isset($ret->errmsg) ? "failed" : "ok";

echo json_encode($ret);