<?php
	
	$LDAPAttrShow= array (
		'Param'	=> array("displayname",	'title', 		'mail',		'telephonenumber',	'ipphone',		"pager",		"facsimiletelephonenumber",	"l",		"streetaddress",	"physicaldeliveryofficename"),
		'Name'	=> array("ФИО",			'Должность',	'E-Mail',	'Городской тел',	'IP телефон',	"Доп. телефон",	"Факс",						"Город",	"Улица",			"Кабинет"),
	);
	
	require_once('prepare.php');
	
	
	$Title = 'Телефонный справочник ('.$P_LDAP[$LDAPCurent]['Name'].")";

	$return		=	iconv('utf-8', 'windows-1251',$Title)."\r\n";
	$return 	.= 	iconv('utf-8', 'windows-1251','Отдел;'.implode(";", $LDAPAttrShow['Name']))."\r\n";	
	$Group='';
	foreach($ArrData as $w) {
		If ($Group !=$w['department'])
			$Group=$w['department'];
		$return		.= iconv('utf-8', 'windows-1251',$Group).";";
		foreach($LDAPAttrShow['Param'] as $a)
			if (isset($w[$a])) 
				$return	.= 	@iconv('utf-8', 'windows-1251',$w[$a]).";";
			else 
				$return	.= ";";
		$return		.=	"\r\n";
	};
	ob_end_clean();
    header ("Content-Type: application/octet-stream");
    header ("Content-Disposition: attachment; filename=TelPhone_full.csv");
    header ("Content-Length: " . strlen( $return ) ); 
	Echo $return;
