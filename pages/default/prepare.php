<?PHP

if (!isset($LDAPCurent)) {
	require_once("../../config/0-config.php");
	require_once("../../ldaps.php");
	$LDAPCurent	=	isset($_GET['LDAP']) ? $_GET['LDAP'] : (isset($_POST['LDAP']) ? $_POST['LDAP'] : (isset($_COOKIE['LDAP']) ? $_COOKIE['LDAP'] : null));
	$UnitCurent =	isset($_GET['Unit']) ? $_GET['Unit'] : (isset($_POST['Unit']) ? $_POST['Unit'] : (isset($_COOKIE['Unit']) ? $_COOKIE['Unit'] : null));
	$Search		=	mb_strtolower(isset($_GET['Search']) ? $_GET['Search'] : (isset($_POST['Search']) ? $_POST['Search'] : null), 'utf8');
	$LDAPCon	= new LDAP($P_LDAP[$LDAPCurent]['Server'], $P_LDAP[$LDAPCurent]['User'], $P_LDAP[$LDAPCurent]['Pass']);
}

$PSplit_W =	isset($_GET['split']) ? $_GET['split'] : (isset($_POST['split']) ? $_POST['split'] : (isset($_COOKIE['split']) ? $_COOKIE['split'] : null));
if (!isset($LDAPAttrShow))
	$LDAPAttrShow = array(
		'Param'	=> array("displayname",	'title', 		'mail', 	'telephonenumber',	 'ipphone'),
		'Name'	=> array("ФИО",			'Должность',	'E-Mail', 	'Городской тел',	'IP телефон'),
		'PDF_W'	=> array(198,			225,			151,		95,					100),
	);
$LDAPAttrHide 	= array(
	'department',
	'objectguid',
	'facsimileTelephoneNumber',
	(!empty($P_LDAP[$LDAPCurent]["OU"][$UnitCurent]['Managing']) ? 'manager' : ''),
	'objectclass',
);

// Получаем список учёток из АД
empty($P_LDAP[$LDAPCurent]["FullNameDepartment"])  ? null : $LDAPCon->FullNameDepartment = $P_LDAP[$LDAPCurent]["FullNameDepartment"];
$ArrData	= $LDAPCon->getArray(
	(($UnitCurent == '0') ? '' : "OU=$UnitCurent, ") . $P_LDAP[$LDAPCurent]['DC'],
	$Search,
	array_merge($LDAPAttrShow['Param'], array('department', 'streetaddress', 'physicaldeliveryofficename')),
	array_merge($LDAPAttrShow['Param'], $LDAPAttrHide),
	$LDAPAttrHide[0],
	$P_LDAP[$LDAPCurent]["OU"][$UnitCurent]
);
if (!empty($P_LDAP[$LDAPCurent]["OU"][$UnitCurent]['Managing'])) {
	/*foreach($ArrData AS $Key=>$Value) 
			$ArrData[$Key]['department'] = $ArrData[$Key]['department2'];		/**/
	if (empty($P_LDAP[$LDAPCurent]["OU"][$UnitCurent]['NoSort']))
		usort($ArrData, 'usort_DepartmentTitleDisplayNameCA');
} else {
	usort($ArrData, 'usort_DepartmentTitleDisplayName');
}

// Получаем список отделов
$ArrDep = array();
foreach ($ArrData as $key) {
	$ArrDep[] =	substr($key['department2'], 0, strpos($key['department2'], '|@|') - 1);
	$ArrDep[] =	$key['department'];
}
$ArrDep = array_unique($ArrDep);
$ArrDepTree = array();
if (!empty($P_LDAP[$LDAPCurent]["OU"][$UnitCurent]['Managing'])) {
	$ArrDep2 = array();
	foreach ($ArrData as $key) {
		$ArrDep2[] = substr($key['department2'], 0, strpos($key['department2'], '|@|') - 1) . '2|@|' . substr($key['department2'], 0, strpos($key['department2'], '|@|') - 1);
		$ArrDep2[] =	$key['department2'];
	}
	$ArrDep2 = array_unique($ArrDep2);
	foreach ($ArrDep2 as $ArrDep2_key => $ArrDep2_val) {
		if (substr($ArrDep2_val, 0, strpos($ArrDep2_val, '|@|') - 1) === @$ArrDep[$ArrDep2_key]) {
			$ArrDep2_key_t = $ArrDep2_key;
			$ArrDepTree[$ArrDep2_key_t]['name'] = substr($ArrDep2_val, strpos($ArrDep2_val, '|@|') + 3, 200);
		} else {
			$ArrDepTree[$ArrDep2_key_t][$ArrDep2_key] = substr($ArrDep2_val, strpos($ArrDep2_val, '|@|') + 3, 200);
		}
	}
	unset($ArrDep2);
} else {
	foreach ($ArrDep as $ArrDep_key => $ArrDep_val)
		$ArrDepTree[$ArrDep_key]['name'] = $ArrDep_val;
}
//Echo '<pre>' . var_export($ArrDep,true) . '</pre>';
//Echo '<pre>' . var_export($ArrDepTree,true) . '</pre>';
//usort($ArrDep,'usort_Department');/**/

// Удаляем пустые столбцы
foreach ($LDAPAttrShow['Param'] as $LDAPAS_K => $LDAPAS) {
	$DelCol = True;
	foreach ($ArrData as $key) {
		if (!empty($key[$LDAPAS])) {
			$DelCol = false;
			break;
		}
	};
	if ($DelCol === True) {
		unset($LDAPAttrShow['Param'][$LDAPAS_K]);
		unset($LDAPAttrShow['Name'][$LDAPAS_K]);
		unset($LDAPAttrShow['PDF_W'][$LDAPAS_K]);
	}
};/**/
