<?php 
	header('Content-type: text/html; charset=utf-8');
	set_time_limit(600);
	ini_set('memory_limit', '256M'); 
	if (
		isset($_POST['loginLDAP']) 		&& (trim($_POST['loginLDAP'])!='') && 
		isset($_POST['passwordLDAP']) 	&& (Trim($_POST['passwordLDAP'])!='')&& 
		isset($_POST['IDLDAP']) 		&& (trim($_POST['IDLDAP'])!='')
		) {
		require_once("../config.php");
		require_once("../ldaps.php");
		$LDAPCurent		=	trim($_POST['IDLDAP']);
		$loginLDAP		=	(($_POST['loginLDAP']	==='***')?$P_LDAP[$LDAPCurent]['User']:$_POST['loginLDAP']);
		$passwordLDAP	=	(($_POST['passwordLDAP']==='***')?$P_LDAP[$LDAPCurent]['Pass']:$_POST['passwordLDAP']);

		
		$LDAPCon	= new LDAP($P_LDAP[$LDAPCurent]['Server'], $loginLDAP, $passwordLDAP);
		empty($P_LDAP[$LDAPCurent]["FullNameDepartment"])  ? null : $LDAPCon->FullNameDepartment = $P_LDAP[$LDAPCurent]["FullNameDepartment"];
		
		$ArrData	= $LDAPCon->getArray(
								$P_LDAP[$LDAPCurent]['DC'], 
								'', 
								array('objectguid'),
								array(),
								'',
								'',
								false
							);
		//echo '<pre>'.var_export($ArrData,true).'</pre>'; /**/
		
		function recurse1($rgItem){
			If (count($rgItem)===1)
				$Tm[0] = $rgItem[0];
			else /**/
				$Tm[$rgItem[0]] = recurse1(array_slice($rgItem,1));
			return $Tm;
		}; /**/
		
		$ResultOut = array();
		Foreach ($ArrData as $ArrData_V) {
			$TMP = recurse1(array_reverse(explode(',',$ArrData_V['dn'])));
			$ResultOut = array_merge_recursive($ResultOut,$TMP);
		};
	};
	$Title ="Дерево филиала";
?>
<html>
	<head>
		<title><?php Echo $Title?></title>
		<meta charset="utf-8">
	</head>
	<body>
		<Style>
			dl{
				margin-bottom: 0px;
			}
			dd{
				margin-left: 200px;
			}
			dt {
				float: left;
				width: 180px;
			}
		</Style>
		<B><?php Echo $Title?></b><br><br>
		<B><i>Для использования утилиты заполните поля</b></i><br>
		<form name="FormPassIRGKN" method="post" action="">
			<dl>
				<dt>ID LDAP (Регион) *	</dt><dd><input type="text" 			name="IDLDAP"  value="<?php Echo (!Empty($_POST['IDLDAP'])?$_POST['IDLDAP']:'')?>" size="30" autofocus></dd>
				<dt>Логин *				</dt><dd><input type="text" 			name="loginLDAP"  value="<?php Echo (!Empty($_POST['loginLDAP'])?$_POST['loginLDAP']:'')?>" size="30"></dd>
				<dt>Пароль *			</dt><dd><input type="password" 		name="passwordLDAP"  value="<?php Echo (!Empty($_POST['passwordLDAP'])?$_POST['passwordLDAP']:'')?>" size="30"></dd>
				<dt>Показывать пользователей *</dt><dd><input type="checkbox" 	name="ShowUser"  <?php Echo (!Empty($_POST['ShowUser'])?'checked="checked"':'')?> size="30"></dd>
				<dt>&nbsp;				</dt><dd>&nbsp;</dd>
				<dt>&nbsp;				</dt><dd><input type="submit" value="Выполнить"></dd>
				<dt>&nbsp;				</dt><dd>&nbsp;</dd>
				<dt>Найдено УЗ			</dt><dd><?php Echo ((!empty($ArrData))?Count($ArrData): '-')?></dd>
			</dl>
		</form> 
		<pre><?php
			Function ShowTree($qwe, $Pre,$ShowUser) {
					Foreach ($qwe as $qwe_k => $qwe_v) {
						if (is_array($qwe_v)){
							Echo $Pre.$qwe_k.'<br>';
							ShowTree($qwe_v, $Pre.'	|', $ShowUser);
						}
						else{
							if ($ShowUser)
								Echo $Pre.$qwe_v.'<br>';
						}
					}
			}
//			if (isset($ArrData))		var_export($ArrData);
			if (isset($ResultOut)){
				ShowTree($ResultOut,'',isset($_POST['ShowUser']));
//				echo '<br>';
//				var_export($ResultOut);
			}
		?></pre>
	</body>
</html>