<?PHP
require('prepare.php');


function html_show_DepTree($val = [], $lvl = 0)
{
	unset($val['!|@|']);
	foreach ($val as $val_K => $val_V) {
		unset($val_V['!|@|']);
		echo '<li><a href="#Group_' . md5($val_K . "-$lvl") . '">' . $val_K . '</a>';
		if (Count($val_V) >= 1) {
			echo '<ul class="nav">';
			html_show_DepTree($val_V, $lvl + 1);
			echo '<li class="divider"></li></ul>';
		}
		echo '</li>';
	}
}

function html_show_listUser($val = [], $lvl = 0)
{
	global $LDAPAttrShow;
	global $PageCurent, $LDAPCurent, $UnitCurent;
	foreach ($val as $val_K => $val_V) {
		if ($val_K != '!|@|') {
			$class = (($lvl == 0 ? 'warning' : ($lvl == 1 ? 'success' : ($lvl == 2 ? 'info' : ''))));
			echo '<tr class="' . $class . ' bs-docs-section">
				<th colspan=7>
				<span id="Group_' . md5($val_K . "-$lvl") . '"> </span>'

				. '<a  href="#" OnClick=\'$("#Search").val("' . $val_K . '"); $("form").submit();\' >' . $val_K . '</a>' .
				'</th>
			</tr>';
			if (!empty($val_V['!|@|'])) {
				foreach ($val_V['!|@|'] as $val_K_K =>  $w) {
					if ($w['displayname'] !== $w['department']) {
						if (empty($w['displayname'])) echo '<tr class ="colorgray">';
						else echo '<tr>';
						foreach ($LDAPAttrShow['Param'] as $a) {
							if ((isset($w[$a])) && ($w[$a] != '')) {
								if ($a == 'displayname') {
									echo '<td><a onclick="ShowUserModal(this)"  href="" data-toggle="modal" data-target="#modal-windows" data-text="pages/' . $PageCurent . '/details.php?ID=' . urlencode(base64_encode(urlencode(utf8_encode(implode("||||", array($LDAPCurent, $UnitCurent, $w['objectguid'])))))) . '"><span>' .
										SubStr($w[$a], 0, StrPos($w[$a], ' ')) . '</span></br><span>' . SubStr($w[$a], StrPos($w[$a], ' '), 100) . '</span></a></td>';
								} elseif ($a == 'mail')
									echo '<td> <a href="mailto:' . $w[$a] . '">' . $w[$a] . '</a></td>';
								elseif (($a == 'telephonenumber') or ($a == 'pager') or ($a == 'ipphone'))
									echo '<td>' . str_replace('/', '<br>', $w[$a]) . '</td>';
								else
									echo '<td>' . $w[$a] . '</td>';
							} else
								echo '<td>-</td>';
						};
						echo '</tr>';
					}
				}
			}
			html_show_listUser($val_V, $lvl + 1);
		}
	}
};

?>
<div id="main">
	<div id="sidebar" style='<?php echo isset($PSplit_W) ? 'width: ' . $PSplit_W . 'px;' : ''; ?>'>
		<ul class="nav">
			<?php

			html_show_DepTree([key($ArrData_new2) => []]);
			html_show_DepTree($ArrData_new2[key($ArrData_new2)], 1);
			?>
			<li class="divider"></li>
		</ul>
	</div>
	<div id="split-bar">
		<div></div>
	</div>
	<div ID="content" class="content tableFixHead " data-spy="scroll" data-target="#sidebar">
		<?php
		if (empty($ArrData)) {
			if (isset($LDAPCon->Err)) {
				foreach ($LDAPCon->Err as $key)
					echo "<div class='alert alert-danger'><strong>Внимание !</strong> $key</div>";
			}
		}
		?>
		<table ID="DataUsers" class="table table-hover table-striped table-fixed-head">
			<thead>
				<tr>
					<?php foreach ($LDAPAttrShow['Name'] as $a)	echo "<th>$a</th>";	?>
				</tr>
			</thead>
			<tbody>
				<?php
				html_show_listUser($ArrData_new2);
				?>
			</tbody>
		</table>
		<?php ?>
	</div>
	<a href="#" id="goTop" class="btn btn-default " style="position: fixed; bottom: 20px; right: 20px; opacity: 1; cursor: pointer;">
		<span class="glyphicon glyphicon glyphicon-arrow-up"></span>
	</a>

	<script type="text/javascript">
		$('#goTop').click(function() {
			$('#content').animate({
				scrollTop: 0
			}, 500);
			return false;
		})

		var split_min = 200;
		var split_max = 3600;
		var split_mainmin = 800;

		$('#split-bar').mousedown(function(e) {
			e.preventDefault();
			$(document).mousemove(function(e) {
				e.preventDefault();
				var x = e.pageX - $('#sidebar').offset().left;
				if (x > split_min && x < split_max && e.pageX < ($(window).width() - split_mainmin)) {
					x = x - 6;
					$('#sidebar').css("width", x);
				}
			})
		});
		$(document).mouseup(function(e) {
			document.cookie = "split=" + ($('#split-bar').offset().left - 5);
			$(document).unbind('mousemove');
		});

		function ShowUserModal(qrg) {
			$.ajax({
				type: 'POST',
				url: $(qrg).data("text"),
				beforeSend: function() {
					$("#modal-windows #modal-label").html('Информация о сотруднике');
					$("#modal-windows .modal-dialog").addClass('modal-lg');
					$("#result").html('Загрузка .....');
				},
				success: function(data) {
					$("#result").html(data);
				},
				error: function(data) {
					$("#result").html('Ошибка загрузки');
				}
			});
		};
		$(window).resize(function() {
			$('#DataUsers').css("margin-bottom", $("#content").height() - 70);
			$('.table tbody tr th span').css("padding-top", $(".table thead ").height() + 10);
		});
		$('#DataUsers').css("margin-bottom", $("#content").height() - 70);
		$('.table tbody tr th span').css("padding-top", $(".table thead ").height() + 10);
	</script>

</div>