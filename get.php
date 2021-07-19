<?php

	include('settings.php');
	$link = mysqli_connect("localhost", $db_user, $db_pass, $db_database);
	if ($link === false){
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}

	$val = @$_GET['val'];
	$request = $_GET['request'];
	$field = @$_GET['field'];
	$sort = @$_GET['sort'];
	$logic = @$_GET['logic'];
	$group = @$_GET['group'];

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

	} elseif ($request == 'type_by_group') {

		$val = filter_var($val, FILTER_SANITIZE_STRING);

		if ($field) {
			$field = filter_var($field, FILTER_SANITIZE_STRING);
			if ($sort) {
				$sort = filter_var($sort, FILTER_SANITIZE_STRING);
				if ($logic) {
					$logic = filter_var($logic, FILTER_SANITIZE_STRING);
					$calc_logic = get_logic($logic);
					$sql = "SELECT " . $field . " FROM resources r WHERE TYPE_CODE IN (SELECT gi.type_code FROM groups g INNER JOIN group_items gi ON gi.group_id = g.id WHERE g.name = '" . $val . "') AND status = 1 ORDER BY (SELECT " . $calc_logic . " FROM resources WHERE id = r.id) DESC, id DESC LIMIT 1";
				} else {
					$sql = "SELECT " . $field . " FROM resources r WHERE TYPE_CODE IN (SELECT gi.type_code FROM groups g INNER JOIN group_items gi ON gi.group_id = g.id WHERE g.name = '" . $val . "') AND status = 1 ORDER BY " . $sort . " DESC, id DESC LIMIT 1";
				}
			} else {
				$sql = "SELECT " . $field . " FROM resources r WHERE TYPE_CODE IN (SELECT gi.type_code FROM groups g INNER JOIN group_items gi ON gi.group_id = g.id WHERE g.name = '" . $val . "') AND status = 1 ORDER BY " . $field . " DESC, id DESC LIMIT 1";
			}
		} else {
			$sql = "SELECT * FROM resources r WHERE TYPE_CODE IN (SELECT gi.type_code FROM groups g INNER JOIN group_items gi ON gi.group_id = g.id WHERE g.name = '" . $val . "') AND status = 1 ORDER BY id DESC LIMIT 1";
		}
		if ($result = mysqli_query($link, $sql)) {
			if (mysqli_num_rows($result) > 0) {
				$json = mysqli_fetch_all($result, MYSQLI_ASSOC);
				$data = json_encode($json);
				echo $data;
			}
		}




	} elseif ($request == 'type_by_name_partial') {

		$val = filter_var($val, FILTER_SANITIZE_STRING);
		if ($field) {
			$field = filter_var($field, FILTER_SANITIZE_STRING);
			if ($sort) {
				$sort = filter_var($sort, FILTER_SANITIZE_STRING);

				if ($logic) {
					$logic = filter_var($logic, FILTER_SANITIZE_STRING);
					$calc_logic = get_logic($logic);
					$sql = "SELECT " . $field . " FROM resources r WHERE r.type_name LIKE '%" . $val . "%' AND r.status = 1 ORDER BY (SELECT " . $calc_logic . " FROM resources WHERE id = r.id) DESC, id DESC LIMIT 1";
				} else {
					$sql = "SELECT " . $field . " FROM resources WHERE type_name LIKE '%" . $val . "%' AND status = 1 ORDER BY " . $sort . " DESC, id DESC LIMIT 1";
				}


			} else {
				$sql = "SELECT " . $field . " FROM resources WHERE type_name LIKE '%" . $val . "%' AND status = 1 ORDER BY " . $field . " DESC, id DESC LIMIT 1";
			}
		} else {
			$sql = "SELECT * FROM resources WHERE type_name LIKE '%" . $val . "%' AND status = 1 ORDER BY id DESC LIMIT 1";
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
