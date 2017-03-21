<?php
	$Search		=	mb_strtolower(isset($_GET['Search'])?$_GET['Search']:(isset($_POST['Search'])?$_POST['Search']:null),'utf8');
	if (isset($Search)) {
		require_once("../../config.php");
		require_once("../../ldaps.php");
		
		$FindList = array ();
		foreach ($P_LDAP as $P_LDAP_K => $P_LDAP_P) {
			$LDAPCon	= new LDAP($P_LDAP_P['Server'], '', '');
			foreach ($P_LDAP_P['OU'] as $P_LDAP_OU_K => $P_LDAP_OU_P) {
				@$ArrData	= $LDAPCon->getCache(
						(($P_LDAP_OU_K=='0')?'':"OU=$P_LDAP_OU_K, ").$P_LDAP_P['DC'], 
						$Search,
						array("department", "streetaddress", "physicaldeliveryofficename", "displayname", "title", "telephonenumber", "ipphone", "mail"),
						array("displayname", "title", "mail", "telephonenumber", "ipphone")
					);
				if (!empty($ArrData)){
					if (!empty($FindList[$P_LDAP_P['Name']]))
						$FindList[$P_LDAP_P['Name']] = array_merge($FindList[$P_LDAP_P['Name']],$ArrData);
					Else 
						$FindList[$P_LDAP_P['Name']] = $ArrData;
				}
			}
			unset($LDAPCon);
		}
	Echo '
      <table class="table table-hover table-striped ">
        <thead>
          <tr>
            <th>ФИО</th>
            <th>Отдел / Должность</th>
            <th>E-Mail</th>
            <th>Городской тел</th>
            <th>IP телефон</th>
          </tr>
        </thead>
        <tbody>';
		foreach ($FindList as $FindList_K=>$FindList_V) {
			Echo '<tr>
				<th class="info" colspan=10>'.$FindList_K.'</th>
			</tr>';
			foreach ($FindList_V as $FindList_V_V) {
				Echo '
					<tr>
						<td>'.$FindList_V_V['displayname'].'</td>
						<td>'.$FindList_V_V['department'].'<br>'
							 .$FindList_V_V['title'].'</td>
						<td>'.$FindList_V_V['mail'].'</td>
						<td>'.str_replace('/','<br>',$FindList_V_V['telephonenumber']).'</td>
						<td>'.str_replace('/','<br>',$FindList_V_V['ipphone']).'</td>
					</tr>
			';
			}
		}
			Echo '	</tbody>
				</table>';
	};