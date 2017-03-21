<?php
	$WebFolder	='/srv/www/telphone';
	Function Show_log($mess, $Type = 0){
		global $WebFolder;
		if ($Type === 1) $mess = "!!! ВНИМАНИЕ !!! $mess" ;
		file_put_contents($WebFolder.'/logs/localsave', date("Y-m-d H:i:s").' - '.$mess."\r\n",FILE_APPEND);
		Echo date("Y-m-d H:i:s")." - $mess<br>";
	};
	
	require_once("config.php");
	require_once("ldaps.php");
	Show_log ("Начинаем обновления кеша на сервере ".$_SERVER['SERVER_ADDR']);
	foreach ($P_LDAP as $P_LDAP_K => $P_LDAP_P) {
			$LDAPCon	= new LDAP($P_LDAP_P['Server'], $P_LDAP_P['User'], $P_LDAP_P['Pass']);
			$ArrData	= array();
			foreach ($P_LDAP_P['OU'] as $P_LDAP_OU_K => $P_LDAP_OU_P) {
				$ArrDataTemp	= $LDAPCon->getArray(
										(($P_LDAP_OU_K=='0')?'':"OU=$P_LDAP_OU_K, ").$P_LDAP_P['DC'], 
										"",
										array('displayname'), 
										array(), //array("displayname", 'title', 'mail', 'telephonenumber', 'ipphone', 'department', 'objectguid'),
										'',
										$P_LDAP_OU_P
										, false
								);
				if (!empty($ArrDataTemp))
					$ArrData[$P_LDAP_OU_K] = $ArrDataTemp;
			}
			if (!empty($ArrData)){
				$FileBody = fopen("$WebFolder/localsave/Cache_".(explode('.',$P_LDAP_P['Server'][0])[1]).".php", "w");
				fwrite($FileBody, '<?php ');
				fwrite($FileBody, 'return '.var_export($ArrData,true));
				fwrite($FileBody, ' ?>');
				fclose($FileBody);
				Show_log ("Обновлен кеш по ".$P_LDAP_K.' '.$P_LDAP_P['Name']);
			}
			Else 
				Show_log ("Не удалось обновить кеш по ".$P_LDAP_K.' '.$P_LDAP_P['Name'], 1);
		unset($LDAPCon);
	}
	Show_log ("Закончили  обновления кеша");/**/
	Show_log("=========================================================");