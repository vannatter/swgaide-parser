<?php

	include('settings.php');
	include('functions.php');
	$link = mysqli_connect("localhost", $db_user, $db_pass, $db_database);
	if ($link === false){
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}

	// swgaide import..
	$linkToXmlFile = $swga_feed;
	$ch = curl_init();
	curl_setopt_array($ch, array(
	CURLOPT_URL => $linkToXmlFile
		, CURLOPT_HEADER => 0
		, CURLOPT_RETURNTRANSFER => 1
		, CURLOPT_ENCODING => 'gzip'
	));

	$compressed = curl_exec($ch);
	curl_close($ch);
	$uncompressed = @gzdecode($compressed);
	$xml = simplexml_load_string($uncompressed, "SimpleXMLElement", LIBXML_NOCDATA);
	$json = json_encode($xml);
	$array = json_decode($json);

	mysqli_query($link, 'UPDATE resources SET status = 0 WHERE status = 1');

	if (isset($array->resources->resource)) {
		foreach (@$array->resources->resource as $resource) {

//			if (@$_GET['debug'] == 1) {
//				echo "<pre style='background-color:pink;'>";
//				var_dump($resource);
//				echo "</pre>";
//			}

			$sql = "SELECT * FROM resources WHERE name = '" . addslashes($resource->name) . "' AND type_code = '" . addslashes($resource->swgaide_type_id) . "' LIMIT 1";
			if ($result = mysqli_query($link, $sql)) {
				if (mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_array($result);
					if (@$row['id']) {
						mysqli_query($link, 'UPDATE resources SET status = 1 WHERE id = ' . $row['id']);
					}
				} else {

					$sqlx = "SELECT id FROM resource_types WHERE resource_name = '" . addslashes($resource->type) . "' AND resource_code = '" . addslashes($resource->swgaide_type_id) . "' LIMIT 1";
					if ($resultx = mysqli_query($link, $sqlx)) {
						if (mysqli_num_rows($resultx) > 0) {
							$rowx = mysqli_fetch_array($resultx);
							if (@$rowx['id']) {
								$resource_type_id = $rowx['id'];
							} else {
								$resource_type_id = null;
							}
						} else {

							$sqli = "INSERT INTO resource_types (resource_code, resource_name) VALUES ('" . addslashes($resource->swgaide_type_id) . "', '" . addslashes($resource->type) . "')";
							mysqli_query($link, $sqli);

							$sqlx = "SELECT id FROM resource_types WHERE resource_name = '" . addslashes($resource->type) . "' AND resource_code = '" . addslashes($resource->swgaide_type_id) . "' LIMIT 1";
							if ($resultx = mysqli_query($link, $sqlx)) {
								if (mysqli_num_rows($resultx) > 0) {
									$rowx = mysqli_fetch_array($resultx);
									if (@$rowx['id']) {
										$resource_type_id = $rowx['id'];
									} else {
										$resource_type_id = null;
									}
								} else {
									$resource_type_id = null;
								}
							}
						}
					} else {
						$resource_type_id = null;
					}

					echo $resource->name . "<br/>";
					echo $resource->type . "<br/>";
					echo 'need to add.. <br/><br/>';
					$insert = "
						INSERT INTO resources (name, resource_type_id, type_code, type_name, cr, dr, hr, ma, oq, sr, ut, fl, pe, timestamp, status, swgaide_id)
						VALUES (
							" . ((isset($resource->name)) ? "'" . addslashes($resource->name) . "'" : 'NULL') . ",
							" . $resource_type_id . ",
							" . ((isset($resource->swgaide_type_id)) ? "'" . $resource->swgaide_type_id . "'" : 'NULL') . ",
							" . ((isset($resource->type)) ? "'" . addslashes($resource->type) . "'" : 'NULL') . ",
							" . ((isset($resource->stats->cr)) ? "'" . $resource->stats->cr . "'" : '0') . ",
							" . ((isset($resource->stats->dr)) ? "'" . $resource->stats->dr . "'" : '0') . ",
							" . ((isset($resource->stats->hr)) ? "'" . $resource->stats->hr . "'" : '0') . ",
							" . ((isset($resource->stats->ma)) ? "'" . $resource->stats->ma . "'" : '0') . ",
							" . ((isset($resource->stats->oq)) ? "'" . $resource->stats->oq . "'" : '0') . ",
							" . ((isset($resource->stats->sr)) ? "'" . $resource->stats->sr . "'" : '0') . ",
							" . ((isset($resource->stats->ut)) ? "'" . $resource->stats->ut . "'" : '0') . ",
							" . ((isset($resource->stats->fl)) ? "'" . $resource->stats->fl . "'" : '0') . ",
							" . ((isset($resource->stats->pe)) ? "'" . $resource->stats->pe . "'" : '0') . ",
							'" . $resource->available_timestamp . "', 1, '" . $resource->{'@attributes'}->swgaide_id . "'
						)
					";
					mysqli_query($link, $insert);
				}
			}
		}
	}

	// galaxy harvester import (match on name to see if we need to add it or not)
	$linkToXmlFile = $gh_feed;
	$ch = curl_init();
	curl_setopt_array($ch, array(
	CURLOPT_URL => $linkToXmlFile
		, CURLOPT_HEADER => 0
		, CURLOPT_RETURNTRANSFER => 1
	));

	$data = curl_exec($ch);
	curl_close($ch);
	$xml = simplexml_load_string($data, "SimpleXMLElement", LIBXML_NOCDATA);
	$json = json_encode($xml);
	$array = json_decode($json);

	foreach ($array->resource as $resource) {

		// do stupid category cleanup to match formatting..
		$resource_category_name = $resource->resource_type;
		$resource_category_name = str_replace("Kashyyyk ", "Kashyyykian ", $resource_category_name);
		$resource_category_name = str_replace("Corellian Fiberplast", "Corellia Fiberplast", $resource_category_name);
		$resource_category_name = str_replace("Corellian Berry Fruit", "Corellia Berry Fruit", $resource_category_name);
		$resource_category_name = str_replace("Corellian Evergreen Wood", "Corellia Evergreen Wood", $resource_category_name);
		$resource_category_name = str_replace("Corellian Flower Fruit", "Corellia Flower Fruit", $resource_category_name);
		$resource_category_name = str_replace("Egg Meat", "Egg", $resource_category_name);

		/////

		$sql = "SELECT * FROM resources WHERE name = '" . addslashes($resource->name) . "' LIMIT 1";
		if ($result = mysqli_query($link, $sql)) {

			if (mysqli_num_rows($result) > 0) {
				$row = mysqli_fetch_array($result);
				if (@$row['id']) {
					mysqli_query($link, 'UPDATE resources SET status = 1 WHERE status = 0 AND id = ' . $row['id']);
				}
			} else {
				echo "we dont have this.. <br/>";

				$sqlx = "SELECT id FROM resource_types WHERE resource_name = '" . addslashes($resource_category_name) . "' LIMIT 1";
				if ($resultx = mysqli_query($link, $sqlx)) {
					if (mysqli_num_rows($resultx) > 0) {
						$rowx = mysqli_fetch_array($resultx);
						if (@$rowx['id']) {
							$resource_type_id = $rowx['id'];
						} else {
							$resource_type_id = null;
						}
					} else {

						$sqli = "INSERT INTO resource_types (resource_code, resource_name) VALUES ('', '" . addslashes($resource_category_name) . "')";
						mysqli_query($link, $sqli);

						$sqlx = "SELECT id FROM resource_types WHERE resource_name = '" . addslashes($resource_category_name) . "' LIMIT 1";
						if ($resultx = mysqli_query($link, $sqlx)) {
							if (mysqli_num_rows($resultx) > 0) {
								$rowx = mysqli_fetch_array($resultx);
								if (@$rowx['id']) {
									$resource_type_id = $rowx['id'];
								} else {
									$resource_type_id = null;
								}
							} else {
								$resource_type_id = null;
							}
						}
					}
				} else {
					$resource_type_id = null;
				}

				echo $resource->name . "<br/>";
				echo $resource->resource_type . "<br/>";
				$timestamp = strtotime($resource->enter_date);

				$insert = "
					INSERT INTO resources (source, name, resource_type_id, type_code, type_name, cr, dr, hr, ma, oq, sr, ut, fl, pe, timestamp, status, swgaide_id)
					VALUES (2, 
						" . ((isset($resource->name)) ? "'" . addslashes(ucfirst($resource->name)) . "'" : 'NULL') . ",
						" . $resource_type_id . ", NULL, 
						" . ((isset($resource->resource_type)) ? "'" . addslashes($resource_category_name) . "'" : 'NULL') . ",
						" . ((isset($resource->stats->CR)) ? "'" . $resource->stats->CR . "'" : '0') . ",
						" . ((isset($resource->stats->DR)) ? "'" . $resource->stats->DR . "'" : '0') . ",
						" . ((isset($resource->stats->HR)) ? "'" . $resource->stats->HR . "'" : '0') . ",
						" . ((isset($resource->stats->MA)) ? "'" . $resource->stats->MA . "'" : '0') . ",
						" . ((isset($resource->stats->OQ)) ? "'" . $resource->stats->OQ . "'" : '0') . ",
						" . ((isset($resource->stats->SR)) ? "'" . $resource->stats->SR . "'" : '0') . ",
						" . ((isset($resource->stats->UT)) ? "'" . $resource->stats->UT . "'" : '0') . ",
						" . ((isset($resource->stats->FL)) ? "'" . $resource->stats->FL . "'" : '0') . ",
						" . ((isset($resource->stats->PE)) ? "'" . $resource->stats->PE . "'" : '0') . ",
						'" . $timestamp . "', 1, NULL
					)
				";
				mysqli_query($link, $insert);
				echo 'need to add.. <br/><br/>';

			}

		}

	}

	// run weighted averages
	$sql = "SELECT * FROM resources WHERE weighted_as1 is null OR weighted_as2 is null OR weighted_chef1 is null";
	if ($result = mysqli_query($link, $sql)) {
		if (mysqli_num_rows($result) > 0) {
			$data = mysqli_fetch_all($result);
			foreach ($data as $d) {

				$oq = $d[9];
				$sr = $d[10];
				$dr = $d[6];
				$pe = $d[13];

				if (!$oq) { $oq = 0; }
				if (!$sr) { $sr = 0; }
				if (!$dr) { $dr = 0; }
				if (!$pe) { $pe = 0; }

				$as1 = floor((($oq+$sr)/2));
				$as2 = floor(($oq+$sr+$dr)/3);
				$chef1 = floor( ($pe*0.66) + ($oq*0.33) );

				echo "<pre>";
				print_r($d);
				echo "</pre>";
				echo $oq . " + " . $sr . " / 2 = " . (($oq+$sr)/2) . " <br/>";

				$sqlx = "UPDATE resources SET 
							weighted_as1 = '" . $as1 . "', 
							weighted_as2 = '" . $as2 . "',
							weighted_chef1 = '" . $chef1 . "'
					 	 WHERE id = '" . $d[0] . "'";
				mysqli_query($link, $sqlx);
			}
		}
	}

	mysqli_close($link);
	echo "done importing..";
?>
