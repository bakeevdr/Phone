<?php
	session_start();
	require_once("../../config.php");
	require_once("../../ldaps.php");
	require_once("../../library/Auth.class.php");
	$DetUser=isset($_GET['ID'])?$_GET['ID']:(isset($_POST['ID'])?$_POST['ID']:null);

		$ID =  explode ("||||",utf8_decode(urldecode (base64_decode(urldecode ($DetUser)))));
		$LDAPCurent = $ID[0];
		$UnitCurent = $ID[1];
		$LDAPSearch = $ID[2];

		$LDAPCon	= new LDAP($P_LDAP[$LDAPCurent]['Server'], $P_LDAP[$LDAPCurent]['User'], $P_LDAP[$LDAPCurent]['Pass']);
		empty($P_LDAP[$LDAPCurent]["FullNameDepartment"])  ? null : $LDAPCon->FullNameDepartment = $P_LDAP[$LDAPCurent]["FullNameDepartment"];

		$ArrData	= $LDAPCon->getArray(
									$P_LDAP[$LDAPCurent]['DC'], 
									$LDAPSearch, 
									array('objectguid'),
									array(),
									'',
									$P_LDAP[$LDAPCurent]["OU"][$UnitCurent]
						);
		$ArrData = $ArrData[0];
		$Photo = (Isset($ArrData['jpegphoto'])?$ArrData['jpegphoto']: (Isset($ArrData['thumbnailphoto'])?$ArrData['thumbnailphoto']:  'image/no_foto.jpg') );
		
		$showadv = false;
		if (Auth\User::isAuthorized()) {
			if (array_search($_SESSION["user_job"], $P_SortPost)){
				if (is_array($P_LDAP[$LDAPCurent]['Server'])) {
					$LDAPCurentIP = explode('.',$P_LDAP[$LDAPCurent]['Server'][0])[1];
					$UserCurentIP = explode('.',$_SERVER["REMOTE_ADDR"])[1];
					if ($LDAPCurentIP == $UserCurentIP){ // Если совпадает с текущим IP
						$showadv = true;
					}
					Else {// Если разрешено смотреть ВСЕ регионы
						foreach ($P_LDAP as $P_LDAP_v) {
							if (!Empty($P_LDAP_v['ViewAll'])){
								if (is_array($P_LDAP_v['Server'])) {
									if ((explode('.',$P_LDAP_v['Server'][0])[1])===($UserCurentIP))
										$showadv = true;
								}
								else{
									if ((explode('.',$P_LDAP_v['Server'])[1])===($UserCurentIP))
										$showadv = true;
								}
							}
						}
						Echo $ViewAll = $P_LDAP[$LDAPCurent]['ViewAll'];
					}
				}
			}
		}
?>

<Style>
	.DetailsUser {
		min-height: 400px;
		padding: 10px 10px 0 10px;
	}
	.DetailsUser .LeftData{
		float: left;
		width: 250px;
	}
	.DetailsUser .RightData {
		  margin-left: 260px;
	}
	.DetailsUser .photo{
		border-radius: 10px;
	}	
	.DetailsUser .FioUser   {
		background-color: #d9edf7;
		border: 1px solid #bce8f1;
		border-radius: 5px;
		padding: 5px;
		color: #425E85;
		text-align: center;	
		font-size: 22px;
	}
	.DetailsUser .surname   {
		font-weight: bold;
	}	
	.DetailsUser .panel {	
		margin-bottom: 10px;
	}
	.DetailsUser .panel-heading{
		font-weight: bold;
		padding: 2px 10px;
	}	
	.DetailsUser .panel-body {
		padding: 7px;
	}	
	.DetailsUser .panel-body:after, 
	.DetailsUser .panel-body:before {
		display: inline;
		clear: both;
	}
	.DetailsUser dl{
		margin-bottom: 0px;
	}
	.DetailsUser dt {
		float: left;
		width: 140px;
	}
</Style>

<div Class='DetailsUser'>
	<div class="LeftData">
		<img class="photo"  width="250" src="<?php Echo $Photo?>"alt="Нет фотографии" >
	</div>
	<div class="RightData">
		<div class="FioUser">
			<b><?php Echo SubStr($ArrData['displayname'],0,StrPos($ArrData['displayname'],' '))?></b><br>
			<?php Echo SubStr($ArrData['displayname'],StrPos($ArrData['displayname'],' '),100)?>
		</div>
		<br>
		<?php if (isset($ArrData['company']) or isset($ArrData['department'])or isset($ArrData['title']) or isset($ArrData['manager'])) :?>		
			<div class="panel panel-info">
				<div class="panel-heading">Общее</div>		
				<div class="panel-body"> 			
					<dl>
<?php Echo isset($ArrData['company'])		?'<dt>Организация:</dt><dd>'	.$ArrData['company']								.'</dd>':''?>
<?php Echo isset($ArrData['department'])	?'<dt>Отдел:</dt><dd>'			.$ArrData['department']								.'</dd>':''?>
<?php Echo isset($ArrData['title'])			?'<dt>Должность:</dt><dd>'		.$ArrData['title']									.'</dd>':''?>
<?php Echo isset($ArrData['extensionattribute2'])	?'<dt>Секретарь:</dt><dd>'		.$ArrData['extensionattribute2']									.'</dd>':''?>
<?php Echo isset($ArrData['manager'])		?'<dt>Руководитель:</dt><dd>'	.substr(explode(",", $ArrData['manager'])[0],3,100)	.'</dd>':''?>
					</dl>
				</div>
			</div>
		<?php EndIf?>
		
		<?php if (isset($ArrData['telephonenumber']) or isset($ArrData['pager'])or isset($ArrData['ipphone']) or isset($ArrData['mail']) or isset($ArrData['facsimiletelephonenumber'])) :?>
			<div class="panel panel-info">
				<div class="panel-heading">Контакты</div>		
				<div class="panel-body"> 
					<dl>
<?php Echo isset($ArrData['telephonenumber'])			?'<dt>Городской телефон:</dt><dd>'		.$ArrData['telephonenumber']					.'&nbsp;</dd>':''?>
<?php Echo isset($ArrData['ipphone'])					?'<dt>IP телефон:</dt><dd>'				.$ArrData['ipphone']							.'&nbsp;</dd>':''?>
<?php Echo isset($ArrData['pager'])						?'<dt>Доп. телефон:</dt><dd>'			.$ArrData['pager']								.'&nbsp;</dd>':''?>
<?php Echo isset($ArrData['facsimiletelephonenumber'])	?'<dt>Факс:</dt><dd>'					.$ArrData['facsimiletelephonenumber']			.'&nbsp;</dd>':''?>
<?php if ($showadv) { Echo isset($ArrData['mobile'])	?'<dt>Мобильный телефон:</dt><dd>'		.$ArrData['mobile']								.'&nbsp;</dd>':'';}?>


<?php Echo isset($ArrData['mail'])				?'<dt>E-mail:</dt><dd><a href="mailto:'	.$ArrData['mail'].'">'.$ArrData['mail'].'</a>'	.'</dd>':''?>
<?php Echo isset($ArrData['info'])				?'<dt>Заметки</dt><dd style="'.(($ArrData['info'] == strip_tags($ArrData['info']))?'white-space: pre-wrap;':'').'">'					.$ArrData['info']								.'</dd>':''?>

					</dl>
				</div>
			</div>
		<?php EndIf?>
		
		<?php if (isset($ArrData['postalcode']) or isset($ArrData['co']) or isset($ArrData['st']) or isset($ArrData['l']) or isset($ArrData['streetaddress']) or isset($ArrData['physicaldeliveryofficename'])) :?>
			<div class="panel panel-info">
				<div class="panel-heading">Адрес</div>		
				<div class="panel-body"> 
					<dl>
<?php Echo isset($ArrData['postalcode'])					?'<dt>Почтовый индекс:</dt><dd>'.$ArrData['postalcode']					.'</dd>':''?>
<?php Echo isset($ArrData['co'])							?'<dt>Страна:</dt><dd>'			.$ArrData['co']							.'</dd>':''?>
<?php Echo isset($ArrData['st'])							?'<dt>Область, край:</dt><dd>'	.$ArrData['st']							.'</dd>':''?>
<?php Echo isset($ArrData['l'])								?'<dt>Город:</dt><dd>'			.$ArrData['l']							.'</dd>':''?>
<?php Echo isset($ArrData['streetaddress'])					?'<dt>Улица:</dt><dd>'			.$ArrData['streetaddress']				.'</dd>':''?>
<?php Echo isset($ArrData['physicaldeliveryofficename'])	?'<dt>Кабинет:</dt><dd>'		.$ArrData['physicaldeliveryofficename']	.'</dd>':''?>
					</dl>
				</div>
			</div>
		<?php EndIf?>
		


		<?php if (isset($ArrData['directreports'])  && ( 1 ==2)) :?>
			<div class="panel panel-info">
				<div class="panel-heading">Прямые подчиненные</div>		
				<div class="panel-body"> 
<?php 
	if (is_array($ArrData['directreports'])) {
		foreach ($ArrData['directreports'] as $val)
			Echo isset($val)					?substr(explode(",", $val)[0],3,100).'<br>'					:'';
	}
	Else 
		Echo isset($ArrData['directreports'])	?substr(explode(",", $ArrData['directreports'])[0],3,100)	:'';
		?>
				</div>
			</div>
		<?php EndIf?>		
	
	
	</div>
</div>
