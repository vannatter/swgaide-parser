<?php

	include('settings.php');
	$link = mysqli_connect("localhost", $db_user, $db_pass, $db_database);
	if ($link === false){
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}

	$val = @$_GET['val'];
	$request = $_GET['request'];
	$field = @$_GET['field'];
	header('Content-Type: application/json');

	if ($request == 'types') {

		$sql = "SELECT DISTINCT(type_code), type_name FROM resources ORDER BY type_name";

		if ($result = mysqli_query($link, $sql)) {
			if (mysqli_num_rows($result) > 0) {
				$json = mysqli_fetch_all($result, MYSQLI_ASSOC);
				$data = json_encode($json);
				echo $data;
			}
		}

	} elseif ($request == 'type_by_code') {

		$val = filter_var($val, FILTER_SANITIZE_STRING);
		if ($field) {
			$field = filter_var($field, FILTER_SANITIZE_STRING);
			$sql = "SELECT " . $field . " FROM resources WHERE type_code = '" . $val . "' AND status = 1 ORDER BY id DESC LIMIT 1";
		} else {
			$sql = "SELECT * FROM resources WHERE type_code = '" . $val . "' AND status = 1 ORDER BY id DESC LIMIT 1";
		}

		if ($result = mysqli_query($link, $sql)) {
			if (mysqli_num_rows($result) > 0) {
				$json = mysqli_fetch_all($result, MYSQLI_ASSOC);
				$data = json_encode($json);
				echo $data;
			}
		}

	} elseif ($request == 'type_by_name') {

		$val = filter_var($val, FILTER_SANITIZE_STRING);
		if ($field) {
			$field = filter_var($field, FILTER_SANITIZE_STRING);
			$sql = "SELECT " . $field . " FROM resources WHERE type_name = '" . $val . "' AND status = 1 ORDER BY id DESC LIMIT 1";
		} else {
			$sql = "SELECT * FROM resources WHERE type_name = '" . $val . "' AND status = 1 ORDER BY id DESC LIMIT 1";
		}

		if ($result = mysqli_query($link, $sql)) {
			if (mysqli_num_rows($result) > 0) {
				$json = mysqli_fetch_all($result, MYSQLI_ASSOC);
				$data = json_encode($json);
				echo $data;
			}
		}

	} elseif ($request == 'resource_by_name') {

		$val = filter_var($val, FILTER_SANITIZE_STRING);
		if ($field) {
			$field = filter_var($field, FILTER_SANITIZE_STRING);
			$sql = "SELECT " . $field . " FROM resources WHERE name = '" . $val . "' AND status = 1 ORDER BY id DESC LIMIT 1";
		} else {
			$sql = "SELECT * FROM resources WHERE name = '" . $val . "' AND status = 1 ORDER BY id DESC LIMIT 1";
		}

		if ($result = mysqli_query($link, $sql)) {
			if (mysqli_num_rows($result) > 0) {
				$json = mysqli_fetch_all($result, MYSQLI_ASSOC);
				$data = json_encode($json);
				echo $data;
			}
		}

	}

?>
