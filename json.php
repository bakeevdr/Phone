<?php
	require_once("config.php");
	require_once("./ldaps.php");
	header('Content-type: text/html; charset=utf-8');
	
	$DetUser=isset($_GET['ID'])?$_GET['ID']:(isset($_POST['ID'])?$_POST['ID']:null);
	If (isset ($DetUser)) {
		$ID =  explode ("||||",utf8_decode(urldecode (base64_decode(urldecode ($DetUser)))));
		$LDAPCurent = $ID[0];
		$UnitCurent = $ID[1];
		$LDAPSearch = $ID[2];
	
		$LDAPCon	= new LDAP($P_LDAP[$LDAPCurent]['Server'], $P_LDAP[$LDAPCurent]['User'], $P_LDAP[$LDAPCurent]['Pass']);	
		$ArrData	= $LDAPCon->getArray(
									$P_LDAP[$LDAPCurent]['DC'], 
									$LDAPSearch, 
									array('objectguid'),
									array(),
									'',
									$P_LDAP[$LDAPCurent]["OU"][$UnitCurent]
						);
						
		$ArrData=$ArrData[0];
		$ArrData['photo'] = (Isset($ArrData['jpegphoto'])?$ArrData['jpegphoto']: (Isset($ArrData['thumbnailphoto'])?$ArrData['thumbnailphoto']:  'image/no_foto.jpg') );
		
		$ResultOut = array ();
		$OutField = array('cn','photo');
		foreach($OutField as $OutField_v)
			$ResultOut[$OutField_v] = $ArrData[$OutField_v];
		Echo json_encode($ResultOut, JSON_UNESCAPED_UNICODE,500); 
	}
	else
		echo ' Необходимо указать ID пользователя';

	/*Echo '<br><br>';
	echo '=== Выбираем что будите выводить, передаем мне, потом удалю лишнее ==========================';
	Echo '<pre>';
		@var_export($ArrData);
	Echo '</pre>';/**/
?>