<?php

	function deliveries_create_delivery($delivery){

		$delivery['delivered'] = time();
		
		$hash = array();
		
		foreach ($delivery as $k => $v){
			$hash[$k] = AddSlashes($v);
		}

		$ret = db_insert('deliveries', $hash);

		if (!$ret['ok']) return $ret;

		$delivery['id'] = $ret['insert_id'];


		return array(
			'ok'	=> 1,
			'delivery'	=> $delivery,
		);
	}
