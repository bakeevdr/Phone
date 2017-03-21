<?php 
	$ShowMessages = '';
	$ShowMSG = False;
	If (Empty($_COOKIE['ShowMSG']['CARR']) && $LDAPCurent === 'CARR') {
		$ShowMSG = True;
	}

If ($ShowMSG) {
	setcookie("ShowMSG[CARR]", "Show", time()+3600);
	$ShowMessages= '
		<div id="myModal" class="modal fade bs-example-modal-sm" aria-hidden="true" aria-labelledby="mySmallModalLabel" role="dialog" tabindex="-1" style="display: none;">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button class="close" aria-hidden="true" data-dismiss="modal" type="button">X</button>
						<h4 id="mySmallModalLabel" class="modal-title">!!! ВНИМАНИЕ !!! Тестовый запуск</h4>
					</div>
					<div class="modal-body"> 
						<p>Телефонный справочник ЦА Росреестра запущен в рамках тестовой эксплуатации ФГБУ "ФКП Росреестра".</p>
						<p>Данные импортированы с телефонного справочника, предоставленного ЦА Росреестра на FTP сервере "FTP://10.128.1.8/INFO/Телефонные справочники/Справочник ведомственной телефонной сети Росреестра.xls". </p>
						<br>
						<p>По вопросам функционирования справочника следует обращаться к заместителю начальника Управления информационных технологий, Зинатуллину И.Р. <br> IP: 0(99) 6616 <br> E-Mail:ZinatullinIR@kadastr.ru</p>
					</div>
				</div>
			</div>
			<script type="text/javascript"> 
				$("#myModal").modal({keyboard: false});
			</script>
		</div>';
};/**/

/*
					Для подключения к общему телефонному справочнику ФГБУ, необходимо<br>
						1. Создать в домене пользователя "TelPhone" с минимальными правами <br>
						2. В профиле пользователя во вкладке "Удаленное управление" убрать галочку  "Разрешить удаленное управление".<br>
						3. В профиле пользователя во вкладке "Член группы" добавить группу "Гости домена", перевести её в основную группу, все остальные удалить. <br>
						4. Прислать  на ящик  <a href="mailto:BakeevDR@u02.rosreestr.ru">BakeevDR@u02.rosreestr.ru</a> или Skype <a href="Skype:BakeevDR">BakeevDR</a> следующие данные <br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* Код региона,<br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* Название региона,<br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* Город,<br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* Телефонный код города, <br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* ЛОГИН (TelPhone), <br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* ПАРОЛЬ, <br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* IP AD (основной и резервные), <br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* DC запись ("DC=fkprf, DC=ru")<br>
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;* OU в случае присутствия (OU=ZKP) с которого необходимо вести поиск. В случае разделения основного офиса и тер отделов то 2 OU записи  
							
/**/
?>