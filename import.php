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

	mysqli_query($link, 'UPDATE resources SET status = 0 WHERE status = 1 AND source < 3');

	if (isset($array->resources->resource)) {
		foreach (@$array->resources->resource as $resource) {

//			if (@$_GET['debug'] == 1) {
//				echo "<pre style='background-color:pink;'>";
//				var_dump($resource);
//				echo "</pre>";
//			}

			$resource_category_name = $resource->type;

			$cr_max = 1000;
			$cd_max = 1000;
			$dr_max = 1000;
			$hr_max = 1000;
			$ma_max = 1000;
			$oq_max = 1000;
			$sr_max = 1000;
			$ut_max = 1000;
			$fl_max = 1000;
			$pe_max = 1000;

			// can we make some assumptions on planet based on the resource name?
			$planet_corellia = 0;
			$planet_dantooine = 0;
			$planet_dathomir = 0;
			$planet_endor = 0;
			$planet_lok = 0;
			$planet_naboo = 0;
			$planet_rori = 0;
			$planet_talus = 0;
			$planet_tatooine = 0;
			$planet_yavin4 = 0;
			$planet_kashyyyk = 0;
			$planet_mustafar = 0;

			if (strpos(strtolower($resource_category_name), 'corellia') !== false) {
				$planet_corellia = 1;
			}
			if (strpos(strtolower($resource_category_name), 'dantooin') !== false) {
				$planet_dantooine = 1;
			}
			if (strpos(strtolower($resource_category_name), 'dathomir') !== false) {
				$planet_dathomir = 1;
			}
			if (strpos(strtolower($resource_category_name), 'endor') !== false) {
				$planet_endor = 1;
			}
			if (strpos(strtolower($resource_category_name), 'lokian') !== false) {
				$planet_lok = 1;
			}
			if (strpos(strtolower($resource_category_name), 'naboo') !== false) {
				$planet_naboo = 1;
			}
			if (strpos(strtolower($resource_category_name), 'rori') !== false) {
				$planet_rori = 1;
			}
			if (strpos(strtolower($resource_category_name), 'talus') !== false) {
				$planet_talus = 1;
			}
			if (strpos(strtolower($resource_category_name), 'tatooin') !== false) {
				$planet_tatooine = 1;
			}
			if (strpos(strtolower($resource_category_name), 'yavin') !== false) {
				$planet_yavin4 = 1;
			}
			if (strpos(strtolower($resource_category_name), 'kashyyyk') !== false) {
				$planet_kashyyyk = 1;
			}
			if (strpos(strtolower($resource_category_name), 'mustafar') !== false) {
				$planet_mustafar = 1;
			}

			$sql = "SELECT * FROM resources WHERE name = '" . addslashes($resource->name) . "' AND type_code = '" . addslashes($resource->swgaide_type_id) . "' LIMIT 1";
			if ($result = mysqli_query($link, $sql)) {
				if (mysqli_num_rows($result) > 0) {
					$row = mysqli_fetch_array($result);
					if (@$row['id']) {
						mysqli_query($link, 'UPDATE resources SET status = 1 WHERE id = ' . $row['id']);
						// todo: update stats when they are updates
					}
				} else {

					$sqlx = "SELECT * FROM resource_types WHERE resource_name = '" . addslashes($resource->type) . "' AND resource_code = '" . addslashes($resource->swgaide_type_id) . "' LIMIT 1";
					if ($resultx = mysqli_query($link, $sqlx)) {
						if (mysqli_num_rows($resultx) > 0) {
							$rowx = mysqli_fetch_array($resultx);
							if (@$rowx['id']) {
								$resource_type_id = $rowx['id'];
								$cr_max = $rowx['cr_max'];
								$cd_max = $rowx['cd_max'];
								$dr_max = $rowx['dr_max'];
								$hr_max = $rowx['hr_max'];
								$ma_max = $rowx['ma_max'];
								$oq_max = $rowx['oq_max'];
								$sr_max = $rowx['sr_max'];
								$ut_max = $rowx['ut_max'];
								$fl_max = $rowx['fl_max'];
								$pe_max = $rowx['pe_max'];
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

					if (@$resource->stats->oq) {

						// look for planets
						if (is_array($resource->planets->planet)) {
							foreach ($resource->planets->planet as $planet) {
								if (@$planet->name == "Corellia") {
									$planet_corellia = 1;
								}
								if (@$planet->name == "Dantooine") {
									$planet_dantooine = 1;
								}
								if (@$planet->name == "Dathomir") {
									$planet_dathomir = 1;
								}
								if (@$planet->name == "Endor") {
									$planet_endor = 1;
								}
								if (@$planet->name == "Lok") {
									$planet_lok = 1;
								}
								if (@$planet->name == "Naboo") {
									$planet_naboo = 1;
								}
								if (@$planet->name == "Rori") {
									$planet_rori = 1;
								}
								if (@$planet->name == "Talus") {
									$planet_talus = 1;
								}
								if (@$planet->name == "Tatooine") {
									$planet_tatooine = 1;
								}
								if (@$planet->name == "Yavin 4") {
									$planet_yavin4 = 1;
								}
								if (@$planet->name == "Kashyyyk") {
									$planet_kashyyyk = 1;
								}
								if (@$planet->name == "Mustafar") {
									$planet_mustafar = 1;
								}
							}
						} else {
							if (@$resource->planets->planet->name == "Corellia") {
								$planet_corellia = 1;
							}
							if (@$resource->planets->planet->name == "Dantooine") {
								$planet_dantooine = 1;
							}
							if (@$resource->planets->planet->name == "Dathomir") {
								$planet_dathomir = 1;
							}
							if (@$resource->planets->planet->name == "Endor") {
								$planet_endor = 1;
							}
							if (@$resource->planets->planet->name == "Lok") {
								$planet_lok = 1;
							}
							if (@$resource->planets->planet->name == "Naboo") {
								$planet_naboo = 1;
							}
							if (@$resource->planets->planet->name == "Rori") {
								$planet_rori = 1;
							}
							if (@$resource->planets->planet->name == "Talus") {
								$planet_talus = 1;
							}
							if (@$resource->planets->planet->name == "Tatooine") {
								$planet_tatooine = 1;
							}
							if (@$resource->planets->planet->name == "Yavin 4") {
								$planet_yavin4 = 1;
							}
							if (@$resource->planets->planet->name == "Kashyyyk") {
								$planet_kashyyyk = 1;
							}
							if (@$resource->planets->planet->name == "Mustafar") {
								$planet_mustafar = 1;
							}
						}

						echo 'need to addx.. <br/><br/>';
						echo "<pre>";
						print_r($resource);
						echo "</pre>";

						if (is_object($resource->available_timestamp)) {
							$timestamp = time();
						} else {
							$timestamp = $resource->available_timestamp;
						}

						// 								" . ((isset($resource->available_timestamp)) ? "'" . $resource->available_timestamp . "'" : '0') . ", 1, '" . $resource->{'@attributes'}->swgaide_id . "',

						$insert = "
							INSERT INTO resources (name, resource_type_id, type_code, type_name, cr, cd, dr, hr, ma, oq, sr, ut, fl, pe, timestamp, status, swgaide_id,
								planet_corellia,
								planet_dantooine,
								planet_dathomir,
								planet_endor,
								planet_lok,
								planet_naboo,
								planet_rori,
								planet_talus,
								planet_tatooine,
								planet_yavin4,
								planet_kashyyyk,
								planet_mustafar,
								cr_max,
								cd_max,
								dr_max,
								hr_max,
								ma_max,
								oq_max,
								sr_max,
								ut_max,
								fl_max,
								pe_max
							)
							VALUES (
								" . ((isset($resource->name)) ? "'" . addslashes($resource->name) . "'" : 'NULL') . ",
								" . $resource_type_id . ",
								" . ((isset($resource->swgaide_type_id)) ? "'" . $resource->swgaide_type_id . "'" : 'NULL') . ",
								" . ((isset($resource->type)) ? "'" . addslashes($resource->type) . "'" : 'NULL') . ",
								" . ((isset($resource->stats->cr)) ? "'" . $resource->stats->cr . "'" : '0') . ",
								" . ((isset($resource->stats->cd)) ? "'" . $resource->stats->cd . "'" : '0') . ",
								" . ((isset($resource->stats->dr)) ? "'" . $resource->stats->dr . "'" : '0') . ",
								" . ((isset($resource->stats->hr)) ? "'" . $resource->stats->hr . "'" : '0') . ",
								" . ((isset($resource->stats->ma)) ? "'" . $resource->stats->ma . "'" : '0') . ",
								" . ((isset($resource->stats->oq)) ? "'" . $resource->stats->oq . "'" : '0') . ",
								" . ((isset($resource->stats->sr)) ? "'" . $resource->stats->sr . "'" : '0') . ",
								" . ((isset($resource->stats->ut)) ? "'" . $resource->stats->ut . "'" : '0') . ",
								" . ((isset($resource->stats->fl)) ? "'" . $resource->stats->fl . "'" : '0') . ",
								" . ((isset($resource->stats->pe)) ? "'" . $resource->stats->pe . "'" : '0') . ",
								" . $timestamp . ", 1, '" . $resource->{'@attributes'}->swgaide_id . "',
								" . $planet_corellia . ",
								" . $planet_dantooine . ",
								" . $planet_dathomir . ",
								" . $planet_endor . ",
								" . $planet_lok . ",
								" . $planet_naboo . ",
								" . $planet_rori . ",
								" . $planet_talus . ",
								" . $planet_tatooine . ",
								" . $planet_yavin4 . ",
								" . $planet_kashyyyk . ",
								" . $planet_mustafar . ",
								" . $cr_max . ",
								" . $cd_max . ",
								" . $dr_max . ",
								" . $hr_max . ",
								" . $ma_max . ",
								" . $oq_max . ",
								" . $sr_max . ",
								" . $ut_max . ",
								" . $fl_max . ",
								" . $pe_max . "														
							)
						";
						 mysqli_query($link, $insert);
					} else {
						echo 'stats are blank, not adding shit... <br/><br/>';

					}
				}
			}
		}
	}

//	// galaxy harvester import (match on name to see if we need to add it or not)
//	$linkToXmlFile = $gh_feed;
//	$ch = curl_init();
//	curl_setopt_array($ch, array(
//	CURLOPT_URL => $linkToXmlFile
//		, CURLOPT_HEADER => 0
//		, CURLOPT_RETURNTRANSFER => 1
//	));
//
//	$data = curl_exec($ch);
//	curl_close($ch);
//	$xml = simplexml_load_string($data, "SimpleXMLElement", LIBXML_NOCDATA);
//	$json = json_encode($xml);
//	$array = json_decode($json);
//
//	foreach ($array->resource as $resource) {
//
//		// do stupid category cleanup to match formatting..
//		$resource_category_name = $resource->resource_type;
//		$resource_category_name = str_replace("Kashyyyk ", "Kashyyykian ", $resource_category_name);
//		$resource_category_name = str_replace("Dantooinian ", "Dantooine ", $resource_category_name);
//		$resource_category_name = str_replace("Corellian Fiberplast", "Corellia Fiberplast", $resource_category_name);
//		$resource_category_name = str_replace("Corellian Berry Fruit", "Corellia Berry Fruit", $resource_category_name);
//		$resource_category_name = str_replace("Corellian Evergreen Wood", "Corellia Evergreen Wood", $resource_category_name);
//		$resource_category_name = str_replace("Corellian Flower Fruit", "Corellia Flower Fruit", $resource_category_name);
//		$resource_category_name = str_replace("Egg Meat", "Egg", $resource_category_name);
//
//		// can we make some assumptions on planet based on the resource name?
//		$planet_corellia = 0;
//		$planet_dantooine = 0;
//		$planet_dathomir = 0;
//		$planet_endor = 0;
//		$planet_lok = 0;
//		$planet_naboo = 0;
//		$planet_rori = 0;
//		$planet_talus = 0;
//		$planet_tatooine = 0;
//		$planet_yavin4 = 0;
//		$planet_kashyyyk = 0;
//		$planet_mustafar = 0;
//
//		if (strpos(strtolower($resource_category_name), 'corellia') !== false) {
//			$planet_corellia = 1;
//		}
//		if (strpos(strtolower($resource_category_name), 'dantooin') !== false) {
//			$planet_dantooine = 1;
//		}
//		if (strpos(strtolower($resource_category_name), 'dathomir') !== false) {
//			$planet_dathomir = 1;
//		}
//		if (strpos(strtolower($resource_category_name), 'endor') !== false) {
//			$planet_endor = 1;
//		}
//		if (strpos(strtolower($resource_category_name), 'lokian') !== false) {
//			$planet_lok = 1;
//		}
//		if (strpos(strtolower($resource_category_name), 'naboo') !== false) {
//			$planet_naboo = 1;
//		}
//		if (strpos(strtolower($resource_category_name), 'rori') !== false) {
//			$planet_rori = 1;
//		}
//		if (strpos(strtolower($resource_category_name), 'talus') !== false) {
//			$planet_talus = 1;
//		}
//		if (strpos(strtolower($resource_category_name), 'tatooin') !== false) {
//			$planet_tatooine = 1;
//		}
//		if (strpos(strtolower($resource_category_name), 'yavin') !== false) {
//			$planet_yavin4 = 1;
//		}
//		if (strpos(strtolower($resource_category_name), 'kashyyyk') !== false) {
//			$planet_kashyyyk = 1;
//		}
//		if (strpos(strtolower($resource_category_name), 'mustafar') !== false) {
//			$planet_mustafar = 1;
//		}
//
//		$sql = "SELECT * FROM resources WHERE name = '" . addslashes($resource->name) . "' LIMIT 1";
//		if ($result = mysqli_query($link, $sql)) {
//
//			if (mysqli_num_rows($result) > 0) {
//				$row = mysqli_fetch_array($result);
//				if (@$row['id']) {
//					mysqli_query($link, 'UPDATE resources SET status = 1 WHERE source = 2 AND status = 0 AND id = ' . $row['id']);
//					// todo: change this to update stats as well
//				}
//			} else {
//				echo "we dont have this.. <br/>";
//
//				$sqlx = "SELECT id FROM resource_types WHERE resource_name = '" . addslashes($resource_category_name) . "' LIMIT 1";
//				if ($resultx = mysqli_query($link, $sqlx)) {
//					if (mysqli_num_rows($resultx) > 0) {
//						$rowx = mysqli_fetch_array($resultx);
//						if (@$rowx['id']) {
//							$resource_type_id = $rowx['id'];
//						} else {
//							$resource_type_id = null;
//						}
//					} else {
//
//						$sqli = "INSERT INTO resource_types (resource_code, resource_name) VALUES ('', '" . addslashes($resource_category_name) . "')";
//						mysqli_query($link, $sqli);
//
//						$sqlx = "SELECT id FROM resource_types WHERE resource_name = '" . addslashes($resource_category_name) . "' LIMIT 1";
//						if ($resultx = mysqli_query($link, $sqlx)) {
//							if (mysqli_num_rows($resultx) > 0) {
//								$rowx = mysqli_fetch_array($resultx);
//								if (@$rowx['id']) {
//									$resource_type_id = $rowx['id'];
//								} else {
//									$resource_type_id = null;
//								}
//							} else {
//								$resource_type_id = null;
//							}
//						}
//					}
//				} else {
//					$resource_type_id = null;
//				}
//
//				echo $resource->name . "<br/>";
//				echo $resource->resource_type . "<br/>";
//				$timestamp = strtotime($resource->enter_date);
//
//				$insert = "
//					INSERT INTO resources (source, name, resource_type_id, type_code, type_name, cr, cd, dr, hr, ma, oq, sr, ut, fl, pe, timestamp, status, swgaide_id,
//								planet_corellia,
//								planet_dantooine,
//								planet_dathomir,
//								planet_endor,
//								planet_lok,
//								planet_naboo,
//								planet_rori,
//								planet_talus,
//								planet_tatooine,
//								planet_yavin4,
//								planet_kashyyyk,
//								planet_mustafar
//					)
//					VALUES (2,
//						" . ((isset($resource->name)) ? "'" . addslashes(ucfirst($resource->name)) . "'" : 'NULL') . ",
//						" . $resource_type_id . ", NULL,
//						" . ((isset($resource->resource_type)) ? "'" . addslashes($resource_category_name) . "'" : 'NULL') . ",
//						" . ((isset($resource->stats->CR)) ? "'" . $resource->stats->CR . "'" : '0') . ",
//						" . ((isset($resource->stats->CD)) ? "'" . $resource->stats->CD . "'" : '0') . ",
//						" . ((isset($resource->stats->DR)) ? "'" . $resource->stats->DR . "'" : '0') . ",
//						" . ((isset($resource->stats->HR)) ? "'" . $resource->stats->HR . "'" : '0') . ",
//						" . ((isset($resource->stats->MA)) ? "'" . $resource->stats->MA . "'" : '0') . ",
//						" . ((isset($resource->stats->OQ)) ? "'" . $resource->stats->OQ . "'" : '0') . ",
//						" . ((isset($resource->stats->SR)) ? "'" . $resource->stats->SR . "'" : '0') . ",
//						" . ((isset($resource->stats->UT)) ? "'" . $resource->stats->UT . "'" : '0') . ",
//						" . ((isset($resource->stats->FL)) ? "'" . $resource->stats->FL . "'" : '0') . ",
//						" . ((isset($resource->stats->PE)) ? "'" . $resource->stats->PE . "'" : '0') . ",
//						'" . $timestamp . "', 1, NULL,
//						" . $planet_corellia . ",
//						" . $planet_dantooine . ",
//						" . $planet_dathomir . ",
//						" . $planet_endor . ",
//						" . $planet_lok . ",
//						" . $planet_naboo . ",
//						" . $planet_rori . ",
//						" . $planet_talus . ",
//						" . $planet_tatooine . ",
//						" . $planet_yavin4 . ",
//						" . $planet_kashyyyk . ",
//						" . $planet_mustafar . "
//					)
//				";
//				mysqli_query($link, $insert);
//				echo 'need to add.. <br/><br/>';
//
//			}
//
//		}
//
//	}

	// run weighted averages
	$sql = "SELECT id, oq, sr, dr, pe, cd, ut, fl FROM resources WHERE weighted_med1 is null OR weighted_med2 is null OR weighted_med3 is null OR weighted_med4 is null OR weighted_med5 is null OR weighted_art1 is null OR weighted_as1 is null OR weighted_as2 is null OR weighted_chef1 is null OR weighted_chef2 is null OR weighted_chef3 is null OR weighted_chef4 is null OR weighted_chef5 is null OR weighted_chef6 is null OR weighted_chef7 is null OR weighted_chef8 is null OR weighted_chef9 is null or weighted_ws1 is null or weighted_ws2 is null or weighted_ws3 is null or weighted_ws4 is null or weighted_ws5 is null or weighted_ws6 is null or weighted_ws7 is null";
	if ($result = mysqli_query($link, $sql)) {
		if (mysqli_num_rows($result) > 0) {
			$data = mysqli_fetch_all($result);
			foreach ($data as $d) {

				$oq = $d[1];
				$sr = $d[2];
				$dr = $d[3];
				$pe = $d[4];
				$cd = $d[5];
				$ut = $d[6];
				$fl = $d[7];

				if (!$oq) { $oq = 0; }
				if (!$sr) { $sr = 0; }
				if (!$dr) { $dr = 0; }
				if (!$pe) { $pe = 0; }
				if (!$cd) { $cd = 0; }
				if (!$ut) { $ut = 0; }
				if (!$fl) { $fl = 0; }

				$as1 = floor((($oq+$sr)/2));
				$as2 = floor(($oq+$sr+$dr)/3);

				$art1 = floor( ($sr) );

				$chef1 = floor( ($pe*0.66) + ($oq*0.33) );
				$chef2 = floor( ($fl*0.66) + ($oq*0.33) );
				$chef3 = floor( ($dr*0.75) + ($oq*0.25) );
				$chef4 = floor( ($dr*0.25) + ($oq*0.75) );
				$chef5 = floor( ($fl*0.20) + ($pe*0.30) + ($oq*0.50) );
				$chef6 = floor( ($dr*0.25) + ($sr*0.75) );
				$chef7 = floor( ($dr*0.25) + ($pe*0.75) );
				$chef8 = floor( ($oq) );
				$chef9 = floor( ($dr) );

				$ws1 = floor( ($sr*0.66) + ($oq*0.33) );
				$ws2 = floor((($oq+$cd)/2));
				$ws3 = floor((($oq+$sr)/2));
				$ws4 = floor( ($oq*0.66) + ($sr*0.33) );
				$ws5 = floor((($ut+$sr)/2));
				$ws6 = floor( ($cd*0.66) + ($oq*0.33) );
				$ws7 = floor( ($oq*0.50) + ($dr*0.50) );

				$med1 = floor( ($oq*0.66) + ($ut*0.33) );
				$med2 = floor( ($oq*0.66) + ($pe*0.33) );
				$med3 = floor( ($oq*0.66) + ($dr*0.33) );
				$med4 = floor( ($oq*0.75) + ($dr*0.25) );
				$med5 = floor( ($oq*0.33) + ($sr*0.33) + ($ut*0.33) );

				$sqlx = "UPDATE resources SET 
							weighted_as1 = '" . $as1 . "', 
							weighted_as2 = '" . $as2 . "',
							weighted_art1 = '" . $art1 . "', 
							weighted_chef1 = '" . $chef1 . "',
							weighted_chef2 = '" . $chef2 . "',
							weighted_chef3 = '" . $chef3 . "',
							weighted_chef4 = '" . $chef4 . "',
							weighted_chef5 = '" . $chef5 . "',
							weighted_chef6 = '" . $chef6 . "',
							weighted_chef7 = '" . $chef7 . "',
							weighted_chef8 = '" . $chef8 . "',
							weighted_chef9 = '" . $chef9 . "',
							weighted_med1 = '" . $med1 . "',
							weighted_med2 = '" . $med2 . "',
							weighted_med3 = '" . $med3 . "',
							weighted_med4 = '" . $med4 . "',
							weighted_med5 = '" . $med5 . "',
							weighted_ws1 = '" . $ws1 . "',
							weighted_ws2 = '" . $ws2 . "',
							weighted_ws3 = '" . $ws3 . "',
							weighted_ws4 = '" . $ws4 . "',
							weighted_ws5 = '" . $ws5 . "',
							weighted_ws6 = '" . $ws6 . "',
							weighted_ws7 = '" . $ws7 . "'
					 	 WHERE id = '" . $d[0] . "'";
				mysqli_query($link, $sqlx);
			}
		}
	}

	mysqli_close($link);
	echo "done importing..";
?>
