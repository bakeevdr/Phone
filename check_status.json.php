<?php
	header('Content-Type: application/json');
	$data = array();
	$data['status'] = 'ok';
	$data['message'] = '';
	echo json_encode($data );
?>