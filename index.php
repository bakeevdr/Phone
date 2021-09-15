<?php
error_reporting(0);

include('installcheck.php');
require_once("mainparam.php");
require_once("messages.php");

?>
<!DOCTYPE html>
<html>

<head>
	<title><?php echo $Title ?></title>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<link rel="icon" href="image/favicon.png" type="image/x-icon" />
	<link rel="shortcut icon" href="image/favicon.png" type="image/x-icon" />

	<link rel="stylesheet" href="css/bootstrap.css">
	<link rel="stylesheet" href="css/bootstrap.ais.css">
	<script type="text/javascript" src="js/jquery-1.11.3.min.js"></script>
	<script type="text/javascript" src="js/bootstrap.min.js"></script>
	<!--[if lt IE 9]>
			<script type='text/javascript' src="js/html5.js"></script>
			<script type='text/javascript' src="js/respond.js"></script>
		<![endif]-->
	<!-- Matomo -->
	<script type="text/javascript">
		var _paq = _paq || [];
		/* tracker methods like "setCustomDimension" should be called before "trackPageView" */
		_paq.push(['trackPageView']);
		_paq.push(['enableLinkTracking']);
		(function() {
			var u = "//piwik.kadastr.ru/piwik/";
			_paq.push(['setTrackerUrl', u + 'piwik.php']);
			_paq.push(['setSiteId', '5']);
			var d = document,
				g = d.createElement('script'),
				s = d.getElementsByTagName('script')[0];
			g.type = 'text/javascript';
			g.async = true;
			g.defer = true;
			g.src = u + 'piwik.js';
			s.parentNode.insertBefore(g, s);
		})();
	</script>
	<!-- End Matomo Code -->
</head>

<body>
	<?php echo @$ShowMessages; ?>
	<form method="get">
		<div class="navbar navbar-inverse navbar-AIS navbar-fixed-top" role="navigation">
			<div class="container-fluid">
				<div class="navbar-brand">
					<a href="#" data-toggle="dropdown">
						<div data-toggle="tooltip" data-placement="bottom" title="Меню">
							<span class="icon-bar"></span>
							<span class="icon-bar" Style='	height: 5px; background-color: inherit;'></span>
							<span class="icon-bar"></span>
							<span class="icon-bar" Style='	height: 5px; background-color: inherit;'></span>
							<span class="icon-bar"></span>
						</div>
					</a>
					<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">
						<?php
						if (file_exists("pages" . _DS . $PageCurent . _DS . "control.php")) {
							require_once("pages" . _DS . $PageCurent . _DS . "control.php");
							if (isset($DropDownMenu))
								foreach ($DropDownMenu as $w)
									echo "<li>" . $w . "</li>";
						};
						?>
						<li class="divider"></li>
						<li><a href="files/docreguserad.pdf" download='Регламент регистрации пользователей в AD.pdf'>Регламент регистрации пользователей в AD</a></li>
						<li><a href="changelog.htm">История изменений</a></li>
					</ul>
				</div>
				<div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
					<div class="navbar-header navbar-brand" style=" cursor: default;">
						<?php echo $Title ?>
					</div>
					<ul class="nav navbar-nav">
						<li href="#" data-toggle="tooltip" data-placement="bottom" title="Выбор региона"><?php echo $TitleADD1; ?></li>
						<?php echo @$TitleADD2; ?>
					</ul>
					<div class="navbar-brand ">
						<span ID="TimeCurent" data-toggle="tooltip" data-placement="bottom" title="Время в регионе"></span>
					</div>
					<div class="navbar-form navbar-right" role="search">
						<div class="form-group">
							<input type="text" class="form-control" name="Search" id="Search" placeholder="Найти" Value='<?php echo $Search ?>' data-toggle="tooltip" data-placement="bottom" title="Поиск осуществляется по всем выводимым полям в таблице">
							<script>
								document.getElementById('Search').focus()
							</script>
						</div>
						<button type="submit" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="Найти">
							<span class="glyphicon glyphicon-search"></span>
						</button>
						<button type="button" class="btn btn-default" data-toggle="tooltip" data-placement="bottom" title="Очистить" onclick='$("#Search").val(""); this.form.submit();'>
							<span class="glyphicon glyphicon-remove"></span>
						</button>
						<?php if ($GlobalSearch) : ?>
							<button type="button" class="btn btn-default" onclick="GlobalSearch()" data-toggle="modal" data-placement="bottom" data-target="#modal-windows" title="Поиск по всем регионам">
								<span class="glyphicon glyphicon-search ok"></span>
								<span class="glyphicon glyphicon-globe"></span>
							</button>
						<?php endif ?>
					</div>
				</div><!-- /.navbar-collapse -->
			</div><!-- /.container-fluid -->
		</div>
		<?PHP include("pages" . _DS . $PageCurent . _DS . "home.php"); ?>
		<div ID="HideField" style="display:none">
			<input type="hidden" name="LDAP" value="<?php echo $LDAPCurent ?>">
			<input type="hidden" name="Dep" value="<?php echo $UnitCurent ?>">
		</div>
	</form>
	<div id="LDAP_List" style="display:none"><?PHP echo Get_LDAP_List($P_LDAP, $LDAPCurent); ?></div>

	<div class="modal fade" id="modal-windows" tabindex="-1" role="dialog" aria-labelledby="modal-label" aria-hidden="true">
		<div class="modal-dialog ">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
					<h4 class="modal-title" id="modal-label">Шапка диалога</h4>
				</div>
				<div class="modal-body" id="result">Загрузка....</div>
			</div>
		</div>
	</div>

	<script type="text/javascript">
		$(document).ready(function() {
			$('#modal-windows').on('hidden.bs.modal', function(event) {
				$("#result").html('Загрузка .....');
				$("#modal-windows .modal-dialog").removeClass('modal-lg');
				$("#modal-windows .modal-dialog").removeClass('modal-sm');
			});
		});

		function ShowLDAP_List() {
			$("#modal-windows #modal-label").html('Список регионов');
			$("#modal-windows #result").html($("#LDAP_List").html());
		};
		$(function() {
			$("[data-toggle='tooltip']").tooltip();
		});

		function GlobalSearch() {
			$.ajax({
				type: 'POST',
				url: './pages/default/globalsearch.php?' + $("form").serialize(),
				beforeSend: function() {
					$("#modal-windows #modal-label").html('Глобальный поиск');
					$("#modal-windows .modal-dialog").addClass('modal-lg');
					$("#result").html('Загрузка .....');
				},
				success: function(data) {
					$("#result").empty()
					$("#result").html(data);
				},
				error: function(data) {
					$("#result").html('Ошибка загрузки');
				}
			});
		};

		function Logon() {
			$.ajax({
				type: 'POST',
				url: 'login.php?' + $("form").serialize(),
				beforeSend: function() {
					$("#modal-windows #modal-label").html('Авторизация');
					$("#modal-windows .modal-dialog").addClass('modal-sm');
					$("#result").html('Загрузка .....');
				},
				success: function(data) {
					$("#result").empty()
					$("#result").html(data);
				},
				error: function(data) {
					$("#result").html('Ошибка загрузки');
				}
			});
		};


		var baseTime = <?php echo time() * 1000 ?>;
		var startTime = new Date().getTime();
		var UTC = <?php echo $P_LDAP[$LDAPCurent]['TimeZone']; ?>;

		function redraw() {
			var date = new Date() - startTime;
			var time = baseTime + date;
			var d = new Date(time);
			d.setUTCHours(d.getUTCHours() + UTC);
			$('#TimeCurent').html(d.toUTCString().substring(16, 22));
		}

		setInterval("redraw();", 1000);
		redraw();

		var FBOptions = {
			project: 2
		};
		(function() {
			var script = document.createElement('script');
			script.type = 'text/javascript';
			script.async = true;
			script.src = 'https://feedback.kadastr.ru/media/feedback/js/widget-init.js';
			document.getElementsByTagName('html')[0].appendChild(script);
		})();
	</script>
</body>

</html>