<?php
	header('Content-type: text/html; charset=utf-8');
	//error_reporting(E_ALL); 

		/*$dc ='10.2.143.5';
		$dc ='10.2.143.9';
		$User = 'TelPhone@zkprb.local';
		$Password = 'r53dn98wtv3gyn80gvhp89ty0*(^78g';
		$DN = 'OU=FGU ZKP, DC=zkprb, DC=local';
		/**/
/*	
		$dc ='10.78.141.7';
		$User = 'TelPhone@fgu78.local';
		$Password = 'Ntktajy1';
		$DN = 'DC=fgu78, DC=local';
	
	/**/
	$Port = 389;
	$Search = "(&(objectCategory=person)(!(useraccountcontrol:1.2.840.113556.1.4.803:=2))(!(useraccountcontrol:1.2.840.113556.1.4.803:=16))(!(description=@*)))";
	$Search = "(samaccountname=*)";
	$Attributes = array('dn');
?>
<html>
	<head>
		<title><?php Echo $Title?></title>
		<meta charset="utf-8">
	</head>
	<body>
		<?php 
			Echo 'Go test <br>';
			Echo date('l jS \of F Y h:i:s A').'<br>';
			Echo $dc.'<br>';
			Echo $User.'<br>';
			Echo $Password.'<br>';
			$Err = array();
			$LC=ldap_connect($dc,$Port) or $Err[]='ldap_connect - Ошибка подключения к серверу LDAP ';
			ldap_set_option($LC, LDAP_OPT_PROTOCOL_VERSION, 3); 
			ldap_set_option($LC, LDAP_OPT_REFERRALS, 0); 
			ldap_set_option($LC, LDAP_OPT_SIZELIMIT, 0); 
			ldap_set_option($LC, LDAP_OPT_TIMELIMIT, 0); /**/
			$LB=ldap_bind($LC, $User, $Password) or $Err[]='ldap_bind - Ошибка привязки к LDAP директории ';
		
		$LS	=	ldap_search(
						$LC
						,$DN
						,$Search
						,$Attributes
				);

			$LE = ldap_get_entries($LC, $LS);
			echo '<br>'.count($LE)." записей возвращено\n";
		?>
		<?php Echo '<pre>'.var_export($Err,true).'</pre>';?>
		<?php Echo '<pre>'.var_export($LE,true).'</pre>'; ?>
	</body>
</html>