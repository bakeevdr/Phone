<?php
	$host		= "10.128.1.8";		// IP-адрес FTP-сервера
	$user		= "fgu02";			// Логин от FTP-сервера
	$password	= "q8BqCs";			// Пароль от FTP-сервера
	$XLSPath	= 'INFO/Телефонные справочники/';
	$XLSName	= 'Справочник ведомственной телефонной сети Росреестра.xls';
	$WebFolder	='/srv/www/telphone';

	// --------------------------------------------------------------------------------------------
	
	Function Show_log($mess, $Type = 0){
		global $WebFolder;
		if ($Type === 1) $mess = "!!! ВНИМАНИЕ !!! $mess" ;
		file_put_contents($WebFolder.'/logs/upd_carr', date("Y-m-d H:i:s").' - '.$mess."\r\n",FILE_APPEND);
		Echo date("Y-m-d H:i:s")." - $mess<br>";
	};
	
	Show_log("Конвертирование Excel файла Управления Росрееста.");
	$TMPFolter = sys_get_temp_dir().'/telphone';
	if (!is_dir($TMPFolter))
		mkdir($TMPFolter);
	$XLS_Local=tempnam($TMPFolter, 'TP');
	
	if($connect = ftp_connect($host)){
		if (ftp_login($connect, $user, $password)) {
			ftp_chdir($connect, iconv('utf-8', 'windows-1251',$XLSPath));
			if (ftp_get($connect,  $XLS_Local, iconv('utf-8', 'windows-1251',$XLSName), FTP_BINARY)) {/**/
				Show_log('Файл загружен с FTP сервера.');
			}
			Else
				Show_log('Ошибка получения справочника с FTP.', 1);
		}
		Else 
			Show_log("Неправильный Логин или Пароль к FTP ресурсу.", 1);
	}
	Else 
		Show_log("Ошибка подключения к хосту (ftp_connect) $host .", 1);
	
	if (file_exists($XLS_Local)) {
		Show_log("Начинаем разбор XML справочника.");
		$res = array();
		$data = array();
		$otdel='';
		$objectguid=0;
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
					//&&	($Ncount !=0) 				// Если известно кол-во символов
					&&	($Ncount !=strlen($phone))	// Если длина не равна длине строки 
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
			
		function Phone($phone_gor, $phone_rez) {
			if (!empty($phone_gor) && (!empty($phone_rez))) return phone_format($phone_gor.','.$phone_rez ,'8495',11);
			if (!empty($phone_gor)) return phone_format($phone_gor,'8495',11);
			if (!empty($phone_rez)) return phone_format($phone_rez,'8495',11);
		}/**/
			
		function Phone_Ip($phone_cisco, $phone_avaya) {
			if ((!empty(trim($phone_cisco)) && (!empty(trim($phone_avaya))))) return $phone_cisco .' / '.'8(99) '.$phone_avaya;
			if (!empty(trim($phone_cisco))) return $phone_cisco;
			if (!empty(trim($phone_avaya))) return '8(99) '.$phone_avaya;
		}
		
		require_once "PHPExcel.php";
		$file_type = PHPExcel_IOFactory::identify( $XLS_Local);
		$objReader = PHPExcel_IOFactory::createReader( $file_type );
		$objPHPExcel = $objReader->load( $XLS_Local);
		$objPHPExcel->setActiveSheetIndex(1);
		$aSheet = $objPHPExcel->getActiveSheet();
		
		If (
			(Trim($aSheet->getCellByColumnAndRow(1,2)->getCalculatedValue()) === 'Должность') &&
			(Trim($aSheet->getCellByColumnAndRow(2,2)->getCalculatedValue()) === 'Фамилия, имя, отчество') &&
			(Trim($aSheet->getCellByColumnAndRow(3,2)->getCalculatedValue()) === 'каб.') &&
			(Trim($aSheet->getCellByColumnAndRow(5,2)->getCalculatedValue()) === 'вн. тел. Avaya 99') &&
			(Trim($aSheet->getCellByColumnAndRow(6,2)->getCalculatedValue()) === 'городской 495') &&
			(Trim($aSheet->getCellByColumnAndRow(7,2)->getCalculatedValue()) === 'гор. резервный 495') &&
			(Trim($aSheet->getCellByColumnAndRow(8,2)->getCalculatedValue()) === 'адрес')
		)
		{
			foreach ($aSheet->getMergeCells() as $none_K => $none_V) {
				If (substr($none_V,1,strpos($none_V,':')-1) === substr($none_V,strpos($none_V,':')+2,10))
					$MergeRow[] = substr($none_V,1,strpos($none_V,':')-1);
			}/**/
			$res[] =	array (
				'department' => date('Дата актуальности d-m-Y', PHPExcel_Shared_Date::ExcelToPHP($aSheet->getCellByColumnAndRow(7,1)->getCalculatedValue())) ,
				'displayname' => date('Дата актуальности d-m-Y', PHPExcel_Shared_Date::ExcelToPHP($aSheet->getCellByColumnAndRow(7,1)->getCalculatedValue())) ,
				'dn'=>null,
			);/**/
			for	($i=3;$i<1111;$i++) {
				if (!empty($aSheet->getCellByColumnAndRow(0,$i)->getValue())) {
					$otdel = $aSheet->getCellByColumnAndRow(0,$i)->getCalculatedValue();
				}
				
				if ($objPHPExcel->getActiveSheet()->getRowDimension($i)->getOutlineLevel()===0) {
					$_manager = $aSheet->getCellByColumnAndRow(2,$i+1)->getCalculatedValue();
					if ($_manager==null) {
						$_manager=$otdel;
						if (!empty(trim($aSheet->getCellByColumnAndRow(0,$i)->getValue())))
							$res[] =	array (
								'department' => $otdel,
								'displayname' => $otdel,
								'dn'=>null,
							);
					}
				}
				
				if (
						!empty(trim($aSheet->getCellByColumnAndRow(1,$i)->getValue())) || 
						!empty(trim($aSheet->getCellByColumnAndRow(2,$i)->getValue())) || 
						array_search($i,$MergeRow)!==false
					) 
				{
					$res[] =	array(
							'manager'=>(($aSheet->getCellByColumnAndRow(2,$i)->getValue()!==$_manager)?'CN='.$_manager:null),
							'department'=>$otdel,
							'displayname' => ($aSheet->getCellByColumnAndRow(2,$i)->getValue()!==null)?$aSheet->getCellByColumnAndRow(2,$i)->getCalculatedValue():$aSheet->getCellByColumnAndRow(1,$i)->getCalculatedValue(),
							'title'=> $aSheet->getCellByColumnAndRow(1,$i)->getCalculatedValue(),
							'physicaldeliveryofficename' => $aSheet->getCellByColumnAndRow(3,$i)->getCalculatedValue(),
							'ipphone' =>	Phone_Ip(/*$aSheet->getCellByColumnAndRow(4,$i)->getCalculatedValue()/**/'', $aSheet->getCellByColumnAndRow(5,$i)->getCalculatedValue()) ,
							'telephonenumber' =>  Phone($aSheet->getCellByColumnAndRow(6,$i)->getCalculatedValue(), $aSheet->getCellByColumnAndRow(7,$i)->getCalculatedValue()),
							'streetaddress' => $aSheet->getCellByColumnAndRow(8,$i)->getCalculatedValue(),
							'dn'=>($aSheet->getCellByColumnAndRow(2,$i)->getValue()?'CN='.$aSheet->getCellByColumnAndRow(2,$i)->getCalculatedValue():  null),
							'objectguid'=>$objectguid++,
					);
				}
			}
			if (count($res)>500) {
				$File = fopen($WebFolder.'/localsave/Cache_128.php',"w");
				fwrite($File, '<?php ');
				fwrite($File, 'return '.var_export(array('0'=>$res),true));
				fwrite($File, ' ?>');
				fclose($File);
				Show_log("Конвертирование закончено успешно.");
			}
		}
		else 
			Show_log("Изменилась структура Excel.", 1);
		unlink($XLS_Local);
	}
	Else 
		Show_log("Нет файла для обработки.", 1);
	Show_log("=========================================================");