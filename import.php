<?php

	include('settings.php');
	include('functions.php');
	$link = mysqli_connect("localhost", $db_user, $db_pass, $db_database);
	if ($link === false){
		die("ERROR: Could not connect. " . mysqli_connect_error());
	}

	$linkToXmlFile = 'https://swgaide.com/pub/exports/currentresources_162.xml.gz';
	$ch = curl_init();
	curl_setopt_array($ch, array(
	CURLOPT_URL => $linkToXmlFile
		, CURLOPT_HEADER => 0
		, CURLOPT_RETURNTRANSFER => 1
		, CURLOPT_ENCODING => 'gzip'
	));

	$compressed = curl_exec($ch);
	curl_close($ch);
	$uncompressed = gzdecode($compressed);
	$xml = simplexml_load_string($uncompressed, "SimpleXMLElement", LIBXML_NOCDATA);
	$json = json_encode($xml);
	$array = json_decode($json);

	mysqli_query($link, 'UPDATE resources SET status = 0');

	foreach ($array->resources->resource as $resource) {

		if (@$_GET['debug'] == 1) {
			echo "<pre style='background-color:pink;'>";
			var_dump($resource);
			echo "</pre>";
		}

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

						$sqli = "SELECT id FROM resource_types WHERE resource_name = '" . addslashes($resource->type) . "' AND resource_code = '" . addslashes($resource->swgaide_type_id) . "' LIMIT 1";
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

	// run weighted averages
	$sql = "SELECT * FROM resources WHERE weighted_as1 is null OR weighted_as2 is null";
	if ($result = mysqli_query($link, $sql)) {
		if (mysqli_num_rows($result) > 0) {
			$data = mysqli_fetch_all($result);
			foreach ($data as $d) {
				$oq = $d[9];
				$sr = $d[10];
				$dr = $d[6];

				if (!$oq) { $oq = 0; }
				if (!$sr) { $sr = 0; }
				if (!$dr) { $dr = 0; }

				$as1 = floor((($oq+$sr)/2)+($dr*0.1));
				$as2 = floor(($oq+$sr+$dr)/3);
				$sqlx = "UPDATE resources SET weighted_as1 = '" . $as1 . "', weighted_as2 = '" . $as2 . "' WHERE id = '" . $d[0] . "'";
				mysqli_query($link, $sqlx);
			}
		}
	}

	mysqli_close($link);
	echo "done importing..";
?>
