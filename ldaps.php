<?php
	function Get_LDAP_List($LDAP, $LDAPCurent) {			//Получение списка доменов из конфигураций
		$RT = "";
		if(count($LDAP)>1) {
			foreach($LDAP AS $key=>$value) {
				if ($key!=$LDAPCurent)
					$RT .= (isset($value['CodeKad'])?$value['CodeKad'].' - ':'&nbsp;&nbsp;&nbsp; &nbsp;&nbsp; ')."<a href='?LDAP=$key'>".$value['Name']."</a></br>";
			}
		}
		return $RT;
	};
	
	function usort_Department($a, $b){						// сортировка списка отделов
		global $P_SortDep;
		$res = 0;
		$Q1 = array_keys($P_SortDep,$a);
		$Q2 = array_keys($P_SortDep,$b);
		if ((count($Q1)!=0) && (Count($Q2)!=0) && ($Q1[0] < $Q2[0]))
			$res = -1;
		Elseif (count($Q2)!=0)
			$res = 1;
			Elseif (count($Q1)==0)
				$res = strnatcmp($a, $b);
		return $res;
	};
	
	function usort_DepartmentTitleDisplayNameCA($a, $b){							// сортировка списка пользователей
		global $P_SortDep;
		global $P_SortPost;
		global $P_SortPost_Add;
		$SortPost = array_merge($P_SortPost, $P_SortPost_Add);
		$res = 0;
		
		$Q1 = array_keys($P_SortDep,$a['department']);
		$Q2 = array_keys($P_SortDep,$b['department']);
		
		if ((count($Q1)!=0) && (Count($Q2)!=0) ) {
			if ($Q1[0] == $Q2[0])	$res = 0;
			else 					$res = ($Q1[0] < $Q2[0])?-1:1;
		}
		elseif (count($Q1)!=0)
			$res = -1;
			elseif (count($Q2)!=0)
					$res = 1;
				else
					$res = strnatcmp($a['department2'], $b['department2']);
				
		if ($res == 0) {
			$W1 = (isset($a['title']))?array_keys($SortPost,$a['title']):array();
			$W2 = (isset($b['title']))?array_keys($SortPost,$b['title']):array();
			if ((count($W1)!=0) && (Count($W2)!=0) )
				if ($W1[0] == $W2[0])	$res = 0;
				else 					$res = ($W1[0] < $W2[0])?-1:1;
			elseif (count($W1)!=0)
				$res = -1;
				elseif (count($W2)!=0)
						$res = 1;
		}/**/
		
		if ($res == 0) {
			$res = ( $a['displayname'] < $b['displayname']) ? -1 : 1;
		}/**/
		return $res;
	};
	
	function usort_DepartmentTitleDisplayName($a, $b){							// сортировка списка пользователей
		global $P_SortDep;
		global $P_SortPost;
		global $P_SortPost_Add;
		$SortPost = array_merge($P_SortPost, $P_SortPost_Add);
		
		$res = 0;
/*		if (Empty($a['department'])) $a['department'] = '';
		if (Empty($b['department'])) $b['department'] = '';/**/
		$Q1 = array_keys($P_SortDep,$a['department']);
		$Q2 = array_keys($P_SortDep,$b['department']);
		if ((count($Q1)!=0) && (Count($Q2)!=0) ) {
			if ($Q1[0] == $Q2[0])	$res = 0;
			else 					$res = ($Q1[0] < $Q2[0])?-1:1;
		}
		elseif (count($Q1)!=0)
			$res = -1;
			elseif (count($Q2)!=0)
					$res = 1;
				else
					$res = strnatcmp($a['department'], $b['department']);
		if ($res == 0) {
			$W1 = (isset($a['title']))?array_keys($SortPost,$a['title']):array();
			$W2 = (isset($b['title']))?array_keys($SortPost,$b['title']):array();
			if ((count($W1)!=0) && (Count($W2)!=0) )
				if ($W1[0] == $W2[0])	$res = 0;
				else 					$res = ($W1[0] < $W2[0])?-1:1;
			elseif (count($W1)!=0)
				$res = -1;
				elseif (count($W2)!=0)
						$res = 1;
		}/**/
		if ($res == 0) {
			$res = ( $a['displayname'] < $b['displayname']) ? -1 : 1;
		}/**/
		return $res;
	};
	
class LDAP{
	private $LC; 
	public  $Err;
	private $Cache;
	
	function __construct($Server, $User, $Password, $Port="389"){
		
		if (!empty($User)) {
			if (!function_exists('serviceping')) {
				function serviceping($host,$Port) {
					$op = @fsockopen($host, $Port, $errno, $errstr, 1);
					if (!$op) return 0;
					else {
						fclose($op);
						return 1;
					}
				}
			}
			foreach ($Server as $k => $dc) {
				if (serviceping($dc,$Port) == true) break;
				else $dc = 0;
			}
			if (!$dc) $this -> Err['ldap_ping']='Серверы не доступны, попробуйте позже';
			if (Empty($this->Err)) {
				@$this->LC=ldap_connect($dc,$Port) or $this -> Err['ldap_connect']='Ошибка подключения к серверу LDAP ';
				if (Empty($this->Err)) {
					ldap_set_option($this->LC, LDAP_OPT_PROTOCOL_VERSION, 3); 
					ldap_set_option($this->LC, LDAP_OPT_REFERRALS, 0); 
					ldap_set_option($this->LC, LDAP_OPT_SIZELIMIT, 0); 
					ldap_set_option($this->LC, LDAP_OPT_TIMELIMIT, 0); 
					$LB=@ldap_bind($this->LC, $User, $Password) or $this -> Err['ldap_bind']='Ошибка привязки к LDAP директории ';
				}
			}
			if (!Empty($this->Err)) 
				$this->Cache = (explode('.',$Server[0])[1]);/**/
		}
		Else 
			$this->Cache = (explode('.',$Server[0])[1]);/**/
	}
	
	
	
	function __destruct() {
		if ($this->LC)
			ldap_close($this->LC);
	}
	
	function mb_ucfirst($str, $encoding='UTF-8'){
		$str = mb_ereg_replace('^[\ ]+', '', $str);
		$str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).mb_substr($str, 1, mb_strlen($str), $encoding);
		return $str;
	}
		
	function phone_format($phones,$Pref='', $Ncount=0){							// Форматирование телефона
		$phones = str_replace(';', ',', $phones);
		$phones = str_replace(':', ',', $phones);
		$phones = str_replace('+7', '8', $phones);
		$Phones = explode(',', $phones);
		$formats = array(	'5'		=>	'(##) ###',
							'7'		=>	'#(##) ####',
							'11'	=>	($Pref=='') ? '#(####) ##-##-##' : '#('.str_repeat("#", strlen($Pref)-1).') '. str_repeat("#", 7-strlen($Pref)) .'-##-##',
							'14'	=>	($Pref=='') ? '#(####) ##-##-##(###)' : '#('.str_repeat("#", strlen($Pref)-1).') '. str_repeat("#", 7-strlen($Pref)) .'-##-## (###)',
							'15'	=>	($Pref=='') ? '#(####) ##-##-##(####)' : '#('.str_repeat("#", strlen($Pref)-1).') '. str_repeat("#", 7-strlen($Pref)) .'-##-## (####)',
		);
		foreach($Phones as $H) {
			$phone = preg_replace('/[^0-9]/', '', $H);
			$PrefS =  str_replace('X','',$Pref);
			//$phone = ($Ncount!== 0)?substr($phone,$Ncount):$phone;
			if (
					($Pref !='')  				// Если есть префикс 
				&&	($Ncount !=0) 				// Если известно кол-во символов
				//&&	($Ncount !=strlen($phone))	// Если длина не равна длине строки 
				&&	(substr($phone,0,strlen($PrefS))!==$PrefS)				// если префиксы не совпадают 
				&&	($Ncount === strlen(substr($Pref.$phone,0,$Ncount)))	// если длина строки с префиксом равна 
				&&	(array_key_exists(strlen($Pref.$phone), $formats))		// Если длина с префиксом есть в массиве
			)
			$phone = $Pref.$phone;
			if (array_key_exists(strlen($phone), $formats)) {
				$format = $formats[strlen($phone)];
				$pattern = '/' . str_repeat('([0-9])?', substr_count($format, '#')) . '(.*)/';
				$counter=0;
				$format = preg_replace_callback(
					str_replace('#', '#', '/([#])/'),
					function () use (&$counter) {
						return '${' . (++$counter) . '}';
					},
					$format
				);
				$Result[] = ($phone ? trim(preg_replace($pattern, $format, $phone, 1)) : $H);
			}
			else
				$Result[] = $H;
		};
		return implode(" / ",$Result);
	}
	
	function getArray($DN, $Search, $FilterFolder, $Attributes, $Sort1='', $CurentParam, $Cache=true) {
		if (Empty($this->Err) && Empty($this->Cache) /**/) {
			if ((in_array('jpegphoto',$Attributes)) or (in_array('thumbnailphoto',$Attributes)))
				array_unshift ($Attributes, 'objectguid','whenchanged');
			if (isset($CurentParam['FolderTelNum'])) {
				if(($key = array_search('telephonenumber',$Attributes)) !== FALSE){
					$Attributes[$key] =$CurentParam['FolderTelNum'];
				};
			}
			$Search=(!Empty($Search)?("*$Search*"):'*');
			$Filter = '';
			foreach ($FilterFolder as $FilterFolder_P)
				$Filter .="($FilterFolder_P=$Search)";
			$LS	=	ldap_search(
						$this->LC
						,$DN
						,"(&(|$Filter)(objectCategory=person)(!(useraccountcontrol:1.2.840.113556.1.4.803:=2))(!(useraccountcontrol:1.2.840.113556.1.4.803:=16))(!(description=@*)))"
						,$Attributes
				);
/*			if ( ldap_errno( $this->LC ) === 4 )
				echo 'Partial search results returned';/**/
			ldap_sort($this->LC, $LS, $Sort1);
			$LE	=	ldap_get_entries($this->LC, $LS);
			// Print_r ($LE);
			// Echo  count($LE);
	// Подчищаем массив от лишнего и нормализуем.
			unset ($LE['count']);
			If (isset($CurentParam['FilterDN']) && is_array($CurentParam['FilterDN'])) {
				foreach($LE as $k=>$LE0) {
					$IsDel=True;
					foreach($CurentParam['FilterDN'] as $FilterDN)
						If (strripos($LE0['dn'],$FilterDN)!== False)
							$IsDel=false;
					If ($IsDel)	Unset ($LE[$k]);
				}
			}
			foreach($LE as $k=>$LE0) {
				for ($w=0; $w<$LE0['count']; $w++) {
					unset ($LE[$k][$w]);
				};
				unset ($LE[$k]['count']);
				if (isset($CurentParam['FolderTelNum'])) {
					unset ($LE[$k]['telephonenumber']);
					unset ($LE0['telephonenumber']);
				};
				foreach($LE0 as $k1=>$LE1) {
					if (is_array($LE1) && (Count($LE1)<=2)) {
						
						if ($k1=='displayname') {
							$LE[$k][$k1] = Trim($LE1[0]);
							$LE[$k][$k1] = preg_replace("/^[^a-zа-яё-]+/ui","",preg_replace("/[^a-zа-яё0-9\s-№]/ui","", $LE[$k][$k1]));
							$LE[$k][$k1] = str_replace('№'		,'№ '			,$LE[$k][$k1]);
							$LE[$k][$k1] = str_replace('  '		,' '			,$LE[$k][$k1]);
						}
						elseif (($k1=='department') || ($k1=='title')) {
								$LE[$k][$k1] = $this->mb_ucfirst(Trim($LE1[0]));
								$LE[$k][$k1] = str_replace('i'		,'I'				,$LE[$k][$k1]);
								$LE[$k][$k1] = str_replace('Зам.'	,'Заместитель'		,$LE[$k][$k1]);
								$LE[$k][$k1] = str_replace('зам.'	,'заместителя'		,$LE[$k][$k1]);
								$LE[$k][$k1] = str_replace('№'		,'№ '				,$LE[$k][$k1]);
								$LE[$k][$k1] = str_replace('кат.'	,'категории'		,$LE[$k][$k1]);
								if ($k1=='title') {
									$LE[$k][$k1] = str_replace('1'	,'I'				,$LE[$k][$k1]);
									$LE[$k][$k1] = str_replace('2'	,'II'				,$LE[$k][$k1]);
									$LE[$k][$k1] = str_replace('I-й','I'				,$LE[$k][$k1]);
									$LE[$k][$k1] = str_replace('I-ой','I'				,$LE[$k][$k1]);
									$LE[$k][$k1] = str_replace('-',' - '				,$LE[$k][$k1]);
									$LE[$k][$k1] = str_replace('–',' - '				,$LE[$k][$k1]);
									$LE[$k][$k1] = str_replace(' нач.',' начальника '	,$LE[$k][$k1]);
								}
								$LE[$k][$k1] = str_replace('  '		,' '			,$LE[$k][$k1]);
							}
							elseif (($k1=='telephonenumber') || (isset($CurentParam['FolderTelNum'])) && ($k1==$CurentParam['FolderTelNum'])){
									$LE[$k]['telephonenumber'] = $this->phone_format(Trim($LE1[0]), (isset($CurentParam['PrefTelNum'])?$CurentParam['PrefTelNum']:''),11);
								}
								elseif ($k1=='ipphone') {
										$LE[$k][$k1] = $this->phone_format(Trim($LE1[0]),(isset($CurentParam['PrefIPPhone'])?$CurentParam['PrefIPPhone']:''),7);
									}
									elseif ($k1=='mobile') {
											$LE[$k][$k1] = $this->phone_format(Trim($LE1[0]),'8XXX',11);
										}
										elseif ($k1=='objectguid') {
												$Temp_guid = bin2hex($LE1[0]);
												$Temp_guid_ ='';
												for ($i = 0; $i <= strlen($Temp_guid)-2; $i = $i+2)
													$Temp_guid_ .=  '\\'.substr($Temp_guid, $i, 2);
												$LE[$k][$k1] = $Temp_guid_;
											}
											elseif (($k1=='jpegphoto') || ($k1=='thumbnailphoto')){
													$PhotoFileName='image/users/'.(($k1=='thumbnailphoto')?'thumbnail/':'').str_replace('\\','',$LE[$k]['objectguid']).'-'.substr($LE[$k]['whenchanged'],0,14).'.jpg';
													if (!file_exists(__DIR__.'/'.$PhotoFileName)) {
														$MaskFileDel = __DIR__.'/image/users/'.(($k1=='thumbnailphoto')?'thumbnail/':'').str_replace('\\','',$LE[$k]['objectguid']).'-*.jpg';
														@array_map("unlink",glob($MaskFileDel));/**/
														@$handle = fopen(__DIR__.'/'.$PhotoFileName,'wb');
														@fwrite($handle, $LE1[0]);
														@fclose($handle);
													}
													if (file_exists(__DIR__.'/'.$PhotoFileName))
														$LE[$k][$k1] = $PhotoFileName;
													else unset($LE[$k][$k1]);
												}
												else
													$LE[$k][$k1] = Trim($LE1[0]);
					}
					Else
						if (is_array($LE1) && (Count($LE1)>2)) {
							unset ($LE[$k][$k1]['count']);
						};
						
				};
				foreach($Attributes as $art=>$at) {
					If (!isset ($LE0[$at])) {
						If ($at=='department')
							$LE[$k]['department'] = 'Отдел не указан';
						Else 
							$LE[$k][$at]='';
					}
				};
				If (empty($LE0['department']))
					$LE[$k]['department'] = 'Отдел не указан';
			}
		}
		Elseif ((!Empty($this->Cache)) && $Cache) {
				$LE = $this->getCache($DN, $Search, $FilterFolder,$Attributes);
			}
			else 
				$LE = array ();
		return	$LE;
	}
	
	function getCache($DN, $Search, $FilterFolder, $Attributes) {
		if ( file_exists(__DIR__ . "/localsave/Cache_".$this->Cache.".php")) {
			Unset ($this -> Err);
			$LE = require "localsave/Cache_".$this->Cache.".php";
			If (Count($LE)==1)
					$LE = $LE[0];
			Else{ 
				if (!empty ($LE[substr(explode(',',$DN)[0],3,100)]))
					$LE = $LE[substr(explode(',',$DN)[0],3,100)];
				Else {
					$LE_T = array ();
					foreach($LE as $LE_P) {
						$LE_T = array_merge($LE_T, $LE_P);
					};
					$LE = $LE_T;
					
				}
			}
			foreach($LE as $LE_K=>$LE_P) {
				foreach($Attributes as $atr_v) {
					If (!isset ($LE_P[$atr_v])) {
						If ($atr_v=='department')	$LE[$LE_K][$atr_v] = 'Отдел не указан';
						Else 						$LE[$LE_K][$atr_v]='';
					}
				};
				If (empty($LE_P['department']))
					$LE[$LE_K]['department'] = 'Отдел не указан';
			}

//Echo '<pre>'; var_export($LE); Echo '</pre>';
			if (!empty($Search)) {
				$Out = array ();
				foreach($LE as $LE_K=>$LE_P) {
					foreach($FilterFolder as $FilterFolder_P) {
						if ((!Empty($LE_P[$FilterFolder_P]))  && (!is_array($LE_P[$FilterFolder_P]))){
							if (mb_stripos ($LE_P[$FilterFolder_P], $Search) !== false) {
								$Out[] = $LE_P;
								break;
							};
						};
					};
				};
			}
			else $Out = $LE;
			return	$Out;
		}
		Else 
			$this -> Err['cache_file_exists'] = 'Кеш не найден';/**/
		//Echo '<pre>';var_export($LE);Echo '</pre>';
	}
	
	function getDepartment($DN) {
		@$LS	=	ldap_search($this->LC, $DN, '(&(sAMAccountType=805306368)(!(useraccountcontrol:1.2.840.113556.1.4.803:=2))(!(useraccountcontrol:1.2.840.113556.1.4.803:=16))(!(description=@*)))', array("department"));
		$LE		=	ldap_get_entries($this->LC, $LS);
		unset ($LE['count']);
		foreach($LE as $k=>$LE0)
			$LE[$k] = isset($LE[$k]['department'][0])?Trim($LE[$k]['department'][0]):'Отдел не указан';
		$LE= array_unique($LE);
		if ($LE[0]=='') unset($LE[0]);
		return	$LE;
	}
	
	function setData($DN, $info) {
		$LD_Mod = array();
		$LD_Del = array();
		foreach($info as $k=>$v) {
			if ($v == '')
				$LD_Del[$k]=array();
			$LD_Mod[$k]='-';
			If ((is_array($v)) && (Count($v) !=0 ))
				$LD_Mod[$k]=$v;
			Elseif (!(is_array($v)) && ($v != ''))
					$LD_Mod[$k]=Trim($v);
		}
		if (count ($LD_Mod) != 0)
			ldap_modify($this->LC, $DN, $LD_Mod);
		if (count ($LD_Del) != 0)
			ldap_mod_del($this->LC, $DN, $LD_Del);
	}
}