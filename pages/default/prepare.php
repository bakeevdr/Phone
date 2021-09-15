<?PHP

function Get_LDAP_List($LDAP, $LDAPCurent)
{			//Получение списка доменов из конфигураций
	$RT = "";
	if (count($LDAP) > 1) {
		foreach ($LDAP as $key => $value) {
			if ($key != $LDAPCurent)
				$RT .= (isset($value['CodeKad']) ? $value['CodeKad'] . ' - ' : '&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; ') . "<a href='?LDAP=$key'>" . $value['Name'] . "</a></br>";
		}
	}
	return $RT;
};

function usort_Department($a, $b)
{						// сортировка списка отделов
	global $P_SortDep;
	$res = 0;
	$Q1 = array_keys($P_SortDep, $a);
	$Q2 = array_keys($P_SortDep, $b);
	if ((count($Q1) != 0) && (Count($Q2) != 0) && ($Q1[0] < $Q2[0]))
		$res = -1;
	elseif (count($Q2) != 0)
		$res = 1;
	elseif (count($Q1) == 0)
		$res = strnatcmp($a, $b);
	return $res;
};

function usort_DepartmentTitleDisplayNameCA($a, $b)
{							// сортировка списка пользователей
	global $P_SortDep;
	global $P_SortPost;
	global $P_SortPost_Add;
	$SortPost = array_merge($P_SortPost, $P_SortPost_Add);
	$res = 0;

	$Q1 = array_keys($P_SortDep, $a['department']);
	$Q2 = array_keys($P_SortDep, $b['department']);

	if ((count($Q1) != 0) && (Count($Q2) != 0)) {
		if ($Q1[0] == $Q2[0])	$res = 0;
		else 					$res = ($Q1[0] < $Q2[0]) ? -1 : 1;
	} elseif (count($Q1) != 0)
		$res = -1;
	elseif (count($Q2) != 0)
		$res = 1;
	else
		$res = strnatcmp($a['department2'], $b['department2']);

	if ($res == 0) {
		$W1 = (isset($a['title'])) ? array_keys($SortPost, $a['title']) : array();
		$W2 = (isset($b['title'])) ? array_keys($SortPost, $b['title']) : array();
		if ((count($W1) != 0) && (Count($W2) != 0))
			if ($W1[0] == $W2[0])	$res = 0;
			else 					$res = ($W1[0] < $W2[0]) ? -1 : 1;
		elseif (count($W1) != 0)
			$res = -1;
		elseif (count($W2) != 0)
			$res = 1;
	}/**/

	if ($res == 0) {
		$res = ($a['displayname'] < $b['displayname']) ? -1 : 1;
	}/**/
	return $res;
};

function usort_DepartmentTitleDisplayName($a, $b)
{							// сортировка списка пользователей
	global $P_SortDep;
	global $P_SortPost;
	global $P_SortPost_Add;
	$SortPost = array_merge($P_SortPost, $P_SortPost_Add);

	$res = 0;
	/*		if (Empty($a['department'])) $a['department'] = '';
		if (Empty($b['department'])) $b['department'] = '';/**/
	$Q1 = array_keys($P_SortDep, $a['department']);
	$Q2 = array_keys($P_SortDep, $b['department']);
	if ((count($Q1) != 0) && (Count($Q2) != 0)) {
		if ($Q1[0] == $Q2[0])	$res = 0;
		else 					$res = ($Q1[0] < $Q2[0]) ? -1 : 1;
	} elseif (count($Q1) != 0)
		$res = -1;
	elseif (count($Q2) != 0)
		$res = 1;
	else
		$res = strnatcmp($a['department'], $b['department']);
	if ($res == 0) {
		$W1 = (isset($a['title'])) ? array_keys($SortPost, $a['title']) : array();
		$W2 = (isset($b['title'])) ? array_keys($SortPost, $b['title']) : array();
		if ((count($W1) != 0) && (Count($W2) != 0))
			if ($W1[0] == $W2[0])	$res = 0;
			else 					$res = ($W1[0] < $W2[0]) ? -1 : 1;
		elseif (count($W1) != 0)
			$res = -1;
		elseif (count($W2) != 0)
			$res = 1;
	}/**/
	if ($res == 0) {
		$res = ($a['displayname'] < $b['displayname']) ? -1 : 1;
	}/**/
	return $res;
};

function filtered($List = array(), $Search = '', $fields = array())
{
	$return = array();
	if ((!empty($List)) && (!empty($Search))) {
		foreach ($List as $List_one) {
			foreach ($fields as $Field_one) {
				if ((!empty($List_one[$Field_one]))  && (!is_array($List_one[$Field_one]))) {
					if (mb_stripos($List_one[$Field_one], $Search) !== false) {
						$return[] = $List_one;
						break;
					};
				};
			};
		};
	} else $return = $List;
	return	$return;
}



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

$FilterFolder = array_merge($LDAPAttrShow['Param'], array('department', 'streetaddress', 'physicaldeliveryofficename'));

$LE_FULL = $ArrData;
foreach ($ArrData as $Key => $Value) {
	$ArrData[$Key]['department2'] = $Value['department'];
	if (mb_stripos($Value['department'], 'управление') === false) {
		if (!empty($Value['manager'])) {
			foreach ($LE_FULL as $Value2) {
				if (@$Value2['dn'] === $Value['manager']) {
					if (mb_stripos($Value2['department'], 'руководство') === false)
						$ArrData[$Key]['department2'] = $Value2['department'] . '2|@|' . $Value['department'];
					else
						$ArrData[$Key]['department2'] = $Value['department'] . '2|@|' . $Value['department'];
				}
			}
		} else
			$ArrData[$Key]['department2'] = $Value['department'] . '2|@|' . $Value['department'];
	} else
		$ArrData[$Key]['department2'] = $Value['department'] . '2|@|' . $Value['department'];
}

if ((!empty($ArrData)) &&  (!empty($Search))) {
	$ArrData = filtered($ArrData, $Search, $FilterFolder);
}

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
