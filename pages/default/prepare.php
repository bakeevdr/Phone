<?PHP

function Get_LDAP_List($LDAP, $LDAPCurent)
{
	//Получение списка доменов из конфигураций
	$RT = "";
	if (count($LDAP) > 1) {
		foreach ($LDAP as $key => $value) {
			if ($key != $LDAPCurent)
				$RT .= (isset($value['CodeKad']) ? $value['CodeKad'] . ' - ' : '&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; ') . "<a href='?LDAP=$key'>" . $value['Name'] . "</a></br>";
		}
	}
	return $RT;
};

function TreeDep($ArrDep = [], &$Treeee = [])
{
	global $ArrData;
	foreach ($ArrDep as $ArrDep_K => $ArrDep_V) {
		foreach ($ArrData as $AD_K => $AD_V) {
			if (($ArrDep_V['manager'] == $AD_V['dn']) && ($ArrDep_V['department'] != $AD_V['department'])) {

				if (empty($Treeee))
					$Treeee = [$AD_V['department'] => [$ArrDep_V['department'] =>  ['!|@|' => $ArrDep]]];
				else
					$Treeee = [$AD_V['department'] => $Treeee];
				if (!empty($AD_V['manager'])) {
					TreeDep(array($AD_V), $Treeee);
				}
				$return = $Treeee;
				break 2;
			}
		}
	}
	if (empty($return)) {
		$return = [
			(current($ArrDep)['department']) => ['!|@|' => $ArrDep]
		];  // для типа Руководства

	}
	/** */
	return $return;
}

function sortingARR(&$Arr = [])
{
	uksort($Arr, function ($akey, $bkey) {
		// Сортировка отделов
		global $P_SortDep;
		$Q1 = array_keys($P_SortDep, $akey);
		$Q2 = array_keys($P_SortDep, $bkey);
		if ((count($Q1) != 0) && (Count($Q2) != 0)) {
			if ($Q1[0] == $Q2[0])	$res = 0;
			else 					$res = ($Q1[0] < $Q2[0]) ? -1 : 1;
		} elseif (count($Q1) != 0)
			$res = -1;
		elseif (count($Q2) != 0)
			$res = 1;
		else
			$res = strnatcmp($akey, $bkey);

		return $res;
	});
	foreach ($Arr as $Arr_K => $Arr_V) {
		if ($Arr_K == '!|@|') {
			usort($Arr[$Arr_K], function ($aval, $bval) {
				//сортировка по должности
				global $P_SortPost;
				$W1 = (isset($aval['title'])) ? array_keys($P_SortPost, $aval['title']) : array();
				$W2 = (isset($bval['title'])) ? array_keys($P_SortPost, $bval['title']) : array();

				if ((count($W1) != 0) && (Count($W2) != 0))
					if ($W1[0] == $W2[0])	$res = 0;
					else 					$res = ($W1[0] < $W2[0]) ? -1 : 1;
				elseif (count($W1) != 0)
					$res = -1;
				elseif (count($W2) != 0)
					$res = 1;

				return $res;
			});
		} else if (Count($Arr_V) >= 1) {
			sortingARR($Arr[$Arr_K]);
		}
	}
}

function filtered(&$List = array(), $Search = '', $fields = array())
{
	foreach ($List as $List_K => $List_V) {
		if (Count($List_V) >= 1) {
			if ($List_K == '!|@|') {

				foreach ($List_V as $LU_K => $LU_V) {
					$find = false;
					foreach ($fields as $Field_one) {
						if ((!empty($LU_V[$Field_one]))  && (!is_array($LU_V[$Field_one]))) {
							if (mb_stripos($LU_V[$Field_one], $Search) !== false) {
								$find = true;
								break;
							};
						};
					};
					if (!$find) {
						unset($List['!|@|'][$LU_K]);
					}
				};
				if (Count($List['!|@|']) == 0) {
					unset($List['!|@|']);
				}
			} else {
				filtered($List[$List_K], $Search, $fields);
				if (Count($List[$List_K]) == 0) {
					unset($List[$List_K]);
				}
			}
		}
	}
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
		'Param'	=> array("displayname",	'title', 		'mail', 	'telephonenumber',	 'ipphone', 'physicalDeliveryOfficeName'),
		'Name'	=> array("ФИО",			'Должность',	'E-Mail', 	'Городской тел',	'IP телефон', 'Кабинет'),
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
	null,
	array_merge($LDAPAttrShow['Param'], array('department', 'streetaddress', 'physicaldeliveryofficename')),
	array_merge($LDAPAttrShow['Param'], $LDAPAttrHide),
	$LDAPAttrHide[0],
	$P_LDAP[$LDAPCurent]["OU"][$UnitCurent]
);

$FilterFolder = array_merge($LDAPAttrShow['Param'], array('department', 'streetaddress', 'physicaldeliveryofficename'));

$ArrData_new = array();
//Группируем всех сотрудников по отделам 
foreach ($ArrData as $Key => $Value) {
	if (empty($ArrData_new[$Value['department']]))
		$ArrData_new[$Value['department']] = [];
	$ArrData_new[$Value['department']][$Value['displayname']] = $Value;
};
//строим дерево подчинения отделов по менеджеру
$ArrData_new2 = array();
foreach ($ArrData_new as $ADN_K => $ADN_V) {
	$TreeDep = TreeDep($ADN_V);
	if (!empty($TreeDep))
		$ArrData_new2 = array_replace_recursive($ArrData_new2, $TreeDep);
}

// Фильтрация если был запущен поиск 
if (!empty($Search)) {
	filtered($ArrData_new2, $Search, $FilterFolder);
}

// Сортировка отделов и сотрудников
sortingARR($ArrData_new2);
