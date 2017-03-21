<?PHP
	
	if (!isset($LDAPCurent)) {
		require_once("../../config.php");
		require_once("../../ldaps.php");
		$LDAPCurent	=	isset($_GET['LDAP'])?$_GET['LDAP']:(isset($_POST['LDAP'])?$_POST['LDAP']:(isset($_COOKIE['LDAP'])?$_COOKIE['LDAP']:null));
		$UnitCurent =	isset($_GET['Unit'])?$_GET['Unit']:(isset($_POST['Unit'])?$_POST['Unit']:(isset($_COOKIE['Unit'])?$_COOKIE['Unit']:null));
		$Search		=	mb_strtolower(isset($_GET['Search'])?$_GET['Search']:(isset($_POST['Search'])?$_POST['Search']:null),'utf8');
		$LDAPCon	= new LDAP($P_LDAP[$LDAPCurent]['Server'], $P_LDAP[$LDAPCurent]['User'], $P_LDAP[$LDAPCurent]['Pass']);
	}

	$PSplit_W =	isset($_GET['split'])?$_GET['split']:(isset($_POST['split'])?$_POST['split']:(isset($_COOKIE['split'])?$_COOKIE['split']:null));
	if (!isset($LDAPAttrShow))
		$LDAPAttrShow= array (
			'Param'	=> array("displayname",	'title', 		'mail', 	'telephonenumber',	 'ipphone'),
			'Name'	=> array("ФИО",			'Должность',	'E-Mail', 	'Городской тел',	'IP телефон'),
			'PDF_W'	=> array(198,			225,			151,		95,					100),
		);
	$LDAPAttrHide 	= array('department','objectguid',(!empty($P_LDAP[$LDAPCurent]["OU"][$UnitCurent]['Managing'])?'manager':''));
	
// Получаем список учёток из АД
	$ArrData	= $LDAPCon->getArray(
								(($UnitCurent=='0')?'':"OU=$UnitCurent, ").$P_LDAP[$LDAPCurent]['DC'], 
								$Search,
								array_merge($LDAPAttrShow['Param'],array('department','streetaddress','physicaldeliveryofficename')),
								array_merge($LDAPAttrShow['Param'], $LDAPAttrHide),
								$LDAPAttrHide[0],
								$P_LDAP[$LDAPCurent]["OU"][$UnitCurent]
					);
	if (!empty($P_LDAP[$LDAPCurent]["OU"][$UnitCurent]['Managing']) && ($Search=='')) {
		foreach($ArrData AS $Key=>$Value) {
			$ArrData[$Key]['department2'] = $Value['department'];
			if (mb_stripos ($Value['department'],'управление')===false) {
				If (!Empty($Value['manager'])) {
					foreach($ArrData AS $Key2=>$Value2) {
						if (@$Value2['dn']===$Value['manager']) {
							if (mb_stripos ($Value2['department'],'руководство')===false)
								$ArrData[$Key]['department2'] = $Value2['department'].'2|@|'.$Value['department'];
							Else 
								$ArrData[$Key]['department2'] = $Value['department'].'2|@|'.$Value['department'];
						}
					}
				}			 
				else 
					$ArrData[$Key]['department2'] = $Value['department'].'2|@|'.$Value['department'];
			}
			Else 
				$ArrData[$Key]['department2'] = $Value['department'].'1|@|'.$Value['department'];
		}
		/*foreach($ArrData AS $Key=>$Value) 
			$ArrData[$Key]['department'] = $ArrData[$Key]['department2'];		/**/
		if (Empty($P_LDAP[$LDAPCurent]["OU"][$UnitCurent]['NoSort']))
			usort($ArrData,'usort_DepartmentTitleDisplayNameCA');
	}
	Else {
		usort($ArrData,'usort_DepartmentTitleDisplayName');
	}

// Получаем список отделов

	$ArrDep = array();
	foreach($ArrData AS $key) 
		$ArrDep[] =	$key['department'];
	$ArrDep = array_unique($ArrDep);
	
	$ArrDepTree = array();
	if (!Empty($P_LDAP[$LDAPCurent]["OU"][$UnitCurent]['Managing']) && ($Search=='')) {
		$ArrDep2 = array();
		foreach($ArrData AS $key)
			$ArrDep2[] =	$key['department2'];
		$ArrDep2 = array_unique($ArrDep2);

		foreach($ArrDep2 AS $ArrDep2_key => $ArrDep2_val){
			if (substr($ArrDep2_val,0,strpos($ArrDep2_val,'|@|')-1) === $ArrDep[$ArrDep2_key]) {
				$ArrDep2_key_t = $ArrDep2_key;
				$ArrDepTree[$ArrDep2_key_t]['name'] = $ArrDep[$ArrDep2_key];
			}
			Else
				$ArrDepTree[$ArrDep2_key_t][$ArrDep2_key]=$ArrDep[$ArrDep2_key];
		}
		unset($ArrDep2);
	} Else {
		foreach($ArrDep AS $ArrDep_key => $ArrDep_val)
			$ArrDepTree[$ArrDep_key]['name'] = $ArrDep_val;	
	}	
	// Echo '<pre>'; var_export($ArrDepTree); Echo '</pre>';	
	//usort($ArrDep,'usort_Department');/**/	
	
// Удаляем пустые столбцы
	foreach($LDAPAttrShow['Param'] AS $LDAPAS_K=>$LDAPAS) {		
		$DelCol=True;
		foreach ($ArrData AS $key) {
			if (!empty($key[$LDAPAS]) ) {
				$DelCol=false;
				break;			
			}
		};		
		IF ($DelCol===True) {
			Unset($LDAPAttrShow['Param'][$LDAPAS_K]);
			Unset($LDAPAttrShow['Name'][$LDAPAS_K]);
			Unset($LDAPAttrShow['PDF_W'][$LDAPAS_K]);
		}
	};/**/
	
?>
