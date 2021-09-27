<?php
define('_DS', DIRECTORY_SEPARATOR);
function isset_file($file)
{
	$arr = explode(PATH_SEPARATOR, get_include_path());
	// $arr[]=$_SERVER['DOCUMENT_ROOT']; раскоментировать и проверить
	foreach ($arr as $val) {
		if (file_exists($val . _DS . $file)) return true;
	}
	return false;
}

if (!extension_loaded('ldap')) {
	$err[] = 'Подключите модуль "php_ldap.dll" в файле php.ini';
}
if (!isset_file('config/0-config.php')) {
	$err[] = 'Не найден файл конфигураций "config.php"';
} else {
	require_once("config/0-config.php");
	if (!(isset($P_LDAP) && (count($P_LDAP) != 0))) {
		$err[] = 'В файле настроек не указан ни один номер региона.';
	}
};
// Добавить проверку
if (!is_dir(__DIR__ . '/image/'))
	mkdir(__DIR__ . '/image/');
if (!file_exists(__DIR__ . '/image/users/'))
	if (!mkdir(__DIR__ . '/image/users/'))
		$err[] = 'Не удаеться создать папку ' . __DIR__ . '/image/users/';
if (!file_exists(__DIR__ . '/image/users/thumbnail/'))
	if (!mkdir(__DIR__ . '/image/users/thumbnail/'))
		$err[] = 'Не удаеться создать папку ' . __DIR__ . '/image/users/thumbnail/';
if (!file_exists(__DIR__ . '/localsave/'))
	if (!mkdir(__DIR__ . '/localsave/'))
		$err[] = 'Не удаеться создать папку ' . __DIR__ . '/localsave/';

if (isset($err)) :
?>
	<html>

	<head>
		<title><?php echo $Title ?></title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<link rel="stylesheet" href="css/bootstrap.css">
		<link rel="stylesheet" href="css/bootstrap.ais.css">
	</head>

	<body>
		<div class="navbar navbar-inverse navbar-AIS navbar-fixed-top" role="navigation">
			<div class="container-fluid">
				<div class="navbar-header navbar-brand" style=" cursor: default;">
					<?php echo $Title ?>
				</div>
			</div>
		</div>
		<div style="margin: 20px 30px;">
			<?php
			foreach ($err as $key)
				echo "<div class='alert alert-danger'><strong>Внимание !</strong> $key</div>";
			?>
		</div>
	</body>

	</html>
<?php
	exit;
endif;
?>