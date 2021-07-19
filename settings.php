<?php

	$db_database = 'swg';
	$db_user = 'root';
	$db_pass = 'root';

	function get_logic($type) {

		$logic = '';
		if ($type == 'as') {
			$logic = '((sum(oq+sr)/2)+(dr*0.1))';
		}

		return $logic;
	}
