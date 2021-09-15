<?php
	$DropDownMenu[] =  '<a href="#" onclick="Logon()" data-toggle="modal" data-placement="bottom"  data-target="#modal-windows" ><span class="glyphicon glyphicon-user"></span> Авторизоваться</a></li>';
	
	$DropDownMenu[] = '<li><a href="#" onclick=\'window.location.href="./pages/default/exp_pdf.php?"+$("form").serialize();\'>Экспорт в PDF</a></li>';
	$DropDownMenu[] = '<li><a href="#" onclick=\'window.location.href="./pages/default/exp_csv.php?"+$("form").serialize();\'>Экспорт в CSV</a></li>';
	$DropDownMenu[] = '<li><a href="#" onclick=\'window.location.href="./pages/default/exp_csv_full.php?"+$("form").serialize();\'>Экспорт в CSV полная</a></li>';
	$DropDownMenu[] = '<li><a href="#" onclick=\'window.location.href="./pages/default/exp_csv_all.php?"+$("form").serialize();\'>Экспорт в CSV полная ВСЕ регионы</a></li>';
?>