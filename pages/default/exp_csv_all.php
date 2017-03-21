<?php
	require_once("../../config.php");
	require_once("../../ldaps.php");
	$Title = 'Телефонный справочник (Все регионы)';
	$return		=	iconv('utf-8', 'windows-1251',$Title)."\r\n";	
$LDAPAttrShow= array (
		'Param'	=> array("displayname",	"department",	'title', 		'mail',		'telephonenumber',	'ipphone',		"pager",		"facsimiletelephonenumber",	"l",		"streetaddress",	"physicaldeliveryofficename"),
		'Name'	=> array("ФИО",			"Отдел",		'Должность',	'E-Mail',	'Городской тел',	'IP телефон',	"Доп. телефон",	"Факс",						"Город",	"Улица",			"Кабинет"),
	);
$return 	.= 	iconv('utf-8', 'windows-1251','Регион;'.implode(";", $LDAPAttrShow['Name']))."\r\n";



foreach ($P_LDAP as $P_LDAP_K => $P_LDAP_P) {
			$LDAPCon	= new LDAP($P_LDAP_P['Server'], '', '');
			foreach ($P_LDAP_P['OU'] as $P_LDAP_OU_K => $P_LDAP_OU_P) {
				@$ArrData	= $LDAPCon->getCache(
						(($P_LDAP_OU_K=='0')?'':"OU=$P_LDAP_OU_K, ").$P_LDAP_P['DC'], 
						'',
						array(),
						$LDAPAttrShow['Param']
					);


				if (!empty($ArrData)){
					foreach ($ArrData as $ArrData_K => $ArrData_P) {
						$return	.=	iconv('utf-8', 'windows-1251',$P_LDAP_P['Name']).";";
						foreach($LDAPAttrShow['Param'] as $a) 
							if (isset($ArrData_P[$a])) 
								$return	.= 	@iconv('utf-8', 'windows-1251',$ArrData_P[$a]).";";
							else 
								$return	.= ";";
						$return		.=	"\r\n";
					}
				}
			}
			unset($LDAPCon);
		}/**/




	
/*	$Group='';
	foreach($ArrData as $w) {
		If ($Group !=$w['department'])
			$Group=$w['department'];
		$return		.= iconv('utf-8', 'windows-1251',$Group).";";
		foreach($LDAPAttrShow['Param'] as $a)
			if (isset($w[$a])) 
				$return	.= 	iconv('utf-8', 'windows-1251',$w[$a]).";";
			else 
				$return	.= ";";
		$return		.=	"\r\n";
	};	/**/

    header ("Content-Type: application/octet-stream");
    header ("Content-Disposition: attachment; filename=TelPhone.csv");
    header ("Content-Length: " . strlen( $return ) ); 
	Echo $return;
	
?>