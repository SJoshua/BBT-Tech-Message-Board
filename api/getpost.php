<?php
require_once("../config.php");

session_start();

$ret = new StdClass();

$con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($con->connect_error) {
	die("Failed to access database: " . $con->connect_error);
}

$ret->page = isset($_GET["page"]) ? (int) $_GET["page"] : 1;

$cur = ($ret->page - 1) * 10;
$nxt = $ret->page * 10;

$res = $con->query("select * from messages order by id desc limit $cur, 10");

if (empty($res->num_rows) && $ret->page > 1) {
	$ret->errmsg = "No message in specific page.";
} else {
	$ret->arr = array();
	while ($row = $res -> fetch_assoc()) {
		$ret->arr[] = array(
			"id" => $row["id"],
			"author" => $row["username"],
			"timestamp" => $row["timestamp"],
			"content" => $row["content"]
		);
	}
	$res = $con->query("select * from messages order by id desc limit $nxt, 10");
	$row = $res -> fetch_assoc();
	$ret->last = empty($res->num_rows);
}

$ret->status = !isset($ret->errmsg) ? "ok" : "failed";

echo json_encode($ret);
