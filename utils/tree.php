<?php 
	header('Content-type: text/html; charset=utf-8');
	if (
		isset($_POST['loginLDAP']) 		&& (trim($_POST['loginLDAP'])!='') && 
		isset($_POST['passwordLDAP']) 	&& (Trim($_POST['passwordLDAP'])!='')&& 
		isset($_POST['IDLDAP']) 		&& (trim($_POST['IDLDAP'])!='')
		) {
		require_once("../config.php");
		require_once("../library/ldaps.php");
		$LDAPCurent		=	trim($_POST['IDLDAP']);
		$loginLDAP		=	(($_POST['loginLDAP']	==='***')?$P_LDAP[$LDAPCurent]['User']:$_POST['loginLDAP']);
		$passwordLDAP	=	(($_POST['passwordLDAP']==='***')?$P_LDAP[$LDAPCurent]['Pass']:$_POST['passwordLDAP']);
		$LDAPCon	= new LDAP($P_LDAP[$LDAPCurent]['Server'], $loginLDAP, $passwordLDAP);
		$ArrData	= $LDAPCon->getArray(
								$P_LDAP[$LDAPCurent]['DC'], 
								"",
								array(),
								array('dn'),
								'',
								''
							);
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
				<dt>ID LDAP (Регион) *	</dt><dd><input type="text" 			name="IDLDAP"  value="<?php Echo (!Empty($_POST['IDLDAP'])?$_POST['IDLDAP']:'')?>" size="30"></dd>
				<dt>Логин *				</dt><dd><input type="text" 			name="loginLDAP"  value="<?php Echo (!Empty($_POST['loginLDAP'])?$_POST['loginLDAP']:'')?>" size="30"></dd>
				<dt>Пароль *			</dt><dd><input type="password" 		name="passwordLDAP"  value="<?php Echo (!Empty($_POST['passwordLDAP'])?$_POST['passwordLDAP']:'')?>" size="30"></dd>
				<dt>Показывать пользователей *</dt><dd><input type="checkbox" 	name="ShowUser"  <?php Echo (!Empty($_POST['ShowUser'])?'checked="checked"':'')?> size="30"></dd>
				<dt>&nbsp;				</dt><dd>&nbsp;</dd>
				<dt>&nbsp;				</dt><dd><input type="submit" value="Выполнить"></dd>	
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
		<?php echo base64_decode ('PGRpdiBzdHlsZT0iZm9udC1mYW1pbHk6IHNlcmlmOyBmb250LXNpemU6IDEycHg7IGJhY2tncm91bmQtY29sb3I6IGFsaWNlYmx1ZTsgYm9yZGVyOiAxcHggb3V0c2V0OyBib3R0b206IDVweDsgY3Vyc29yOiBkZWZhdWx0OyBtYXJnaW4tbGVmdDogMHB4OyBwYWRkaW5nOiAzcHggNnB4OyByaWdodDogMjRweDsgcG9zaXRpb246IGZpeGVkOyI+IFBvd2VyZWQgYnkgPGEgZGF0YS10ZXh0PSJwYWdlcy9kZWZhdWx0L2RldGFpbHMucGhwP0lEPU1ESjhmSHg4ZFdaaGZIeDhmRFpqTURreU1USXlZbUl6WW1ZM05EazVZVEV3TWpZd05HWmhNVFk1TVRWbCIgZGF0YS10YXJnZXQ9IiNtb2RhbC13aW5kb3dzIiBkYXRhLXRvZ2dsZT0ibW9kYWwiIGhyZWY9IiIgb25jbGljaz0iU2hvd1VzZXJNb2RhbCh0aGlzKSI+0JHQsNC60LXQtdCyINCU0KAgPC9hPjwvZGl2Pg==');?>
	</body>
</html>