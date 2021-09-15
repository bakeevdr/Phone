<?php 
	require_once("config/0-config.php");
	require_once("./ldaps.php");

	$LDAPCurent	=	isset($_GET['LDAP'])?$_GET['LDAP']:(isset($_POST['LDAP'])?$_POST['LDAP']:(isset($_COOKIE['LDAP'])?$_COOKIE['LDAP']:null));
	$UnitCurent =	isset($_GET['Unit'])?$_GET['Unit']:(isset($_POST['Unit'])?$_POST['Unit']:(isset($_COOKIE['Unit'])?$_COOKIE['Unit']:null));
	$PageCurent	=	isset($_GET['Page'])?$_GET['Page']:(isset($_POST['Page'])?$_POST['Page']:(isset($_COOKIE['Page'])?$_COOKIE['Page']:'default'));
	$Search		=	mb_strtolower(isset($_GET['Search'])?$_GET['Search']:(isset($_POST['Search'])?$_POST['Search']:null),'utf8');

	$LDAPCurent = (!isset($LDAPCurent))?'CAKP':$LDAPCurent;
	$LDAPCurent = (!isset($LDAPCurent))? array_keys($P_LDAP)[0]:((!isset($P_LDAP[$LDAPCurent]))? array_keys($P_LDAP)[0]:$LDAPCurent);
	If (count($P_LDAP)==1)
		$TitleADD1 =' <a href="#" >'.$P_LDAP[$LDAPCurent]['Name'].'</a>'; 
	Else 
		$TitleADD1 =' <a onclick="ShowLDAP_List(this)" data-toggle="modal" data-target="#modal-windows" href="#LDAP_List">'.$P_LDAP[$LDAPCurent]['Name'].'</a>'; 
	
	$UnitCurent = isset($P_LDAP[$LDAPCurent]["OU"][$UnitCurent])? $UnitCurent : array_keys($P_LDAP[$LDAPCurent]['OU'])[0];
	If (count($P_LDAP[$LDAPCurent]['OU'])==1)
		$TitleADD2 ='<li><a href="#" style="cursor:default">'.$P_LDAP[$LDAPCurent]['OU'][$UnitCurent]['Name'].'</a></li>';
	Else {
		$TitleADD2 ='<li data-toggle="tooltip" data-placement="right" title="Подразделения"><a href="#" class="dropdown-toggle" data-toggle="dropdown">'.$P_LDAP[$LDAPCurent]['OU'][$UnitCurent]['Name']."<b class='caret'></b></a><ul Class='dropdown-menu'>";
		foreach($P_LDAP[$LDAPCurent]['OU'] AS $key=>$value) {
			IF ($key!=$UnitCurent)
				$TitleADD2 .="<li><a href='?Unit=$key'>".$value['Name']."</a></li>";
		};
		$TitleADD2 .= "</ul></li>";
	};

	SetCookie("LDAP",$LDAPCurent);
	SetCookie("Unit",$UnitCurent);
	SetCookie("Page",$PageCurent);
	
	$LDAPCon=new LDAP($P_LDAP[$LDAPCurent]['Server'], $P_LDAP[$LDAPCurent]['User'], $P_LDAP[$LDAPCurent]['Pass']);
