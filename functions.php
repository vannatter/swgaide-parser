<?php

	function get_logic($type) {

		$logic = '';
		if ($type == 'as') {
			$logic = '((sum(oq+sr)/2))';
		} elseif ($type == 'as2') {
			$logic = '((sum(oq+sr+dr)/3))';
		}

		return $logic;
	}
