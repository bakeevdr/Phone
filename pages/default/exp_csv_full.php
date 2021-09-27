<?php

$LDAPAttrShow = array(
	'Param'	=> array("displayname",	'title', 		'mail',		'telephonenumber',	'ipphone',		"pager",		"facsimiletelephonenumber",	"l",		"streetaddress",	"physicaldeliveryofficename"),
	'Name'	=> array("ФИО",			'Должность',	'E-Mail',	'Городской тел',	'IP телефон',	"Доп. телефон",	"Факс",						"Город",	"Улица",			"Кабинет"),
);

require_once('prepare.php');


$Title = 'Телефонный справочник (' . $P_LDAP[$LDAPCurent]['Name'] . ")";

$return		=	iconv('utf-8', 'windows-1251', $Title) . "\r\n";
$return 	.= 	iconv('utf-8', 'windows-1251', 'Отдел;' . implode(";", $LDAPAttrShow['Name'])) . "\r\n";

function PrepareCSV($val = [])
{
	global $LDAPAttrShow;
	$return = '';
	foreach ($val as $val_K => $val_V) {
		if ($val_K != '|@|') {
			if (!empty($val_V['|@|'])) {
				foreach ($val_V['|@|'] as $val_K_K =>  $w) {
					$return	.= 	iconv('utf-8', 'windows-1251', $w['department']) . ";";
					foreach ($LDAPAttrShow['Param'] as $a)
						if (isset($w[$a]))
							$return	.= 	iconv('utf-8', 'windows-1251', $w[$a]) . ";";
						else
							$return	.= ";";
					$return		.=	"\r\n";
				}
			}
			$return .= PrepareCSV($val_V);
		}
	}
	return $return;
}
$return		.= PrepareCSV($ArrData_new2);

ob_end_clean();
header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=TelPhone_full.csv");
header("Content-Length: " . strlen($return));
echo $return;
