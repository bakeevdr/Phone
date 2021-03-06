<?PHP
	require('prepare.php');	
?>
<div id="main" >
 	<div id="sidebar" style='<?php Echo isset($PSplit_W)?'width: '.$PSplit_W.'px;':'';?>'>
		<ul class="nav">
			<?php 
				foreach($ArrDepTree as $ArrDepTree_id=>$ArrDepTree_val) {
					Echo '<li><a href="#Group_'.$ArrDepTree_id.'">'.$ArrDepTree_val['name'].'</a>';
					If (Count($ArrDepTree_val)>1) {
						Echo '<ul class="nav">';
						foreach($ArrDepTree_val as $ArrDepTree_val_id=>$ArrDepTree_val_val) {
							if ($ArrDepTree_val_id != 'name') 
								Echo '<li><a href="#Group_'.$ArrDepTree_val_id.'">'.$ArrDepTree_val_val.'</a></li>';
						}
						Echo '<li class="divider"></li></ul>';
					}
					Echo '</li>';
				}
			?>
			<li class="divider"></li>
		</ul>
	</div>			
	<div id="split-bar"><div></div></div>					
	<div ID="content" class="content" data-spy="scroll" data-target="#sidebar">
		<?php 
		If (Empty ($ArrData)) {
			If (isset ($LDAPCon-> Err)) {
				foreach($LDAPCon-> Err AS $key) 
					echo "<div class='alert alert-danger'><strong>Внимание !</strong> $key</div>";
			}
		}
		 ?>
			<table ID ="DataUsers" class="table table-hover table-striped">
				<thead>
					<tr>
					<?php foreach($LDAPAttrShow['Name'] as $a)	Echo "<th>$a</th>";	?>
					</tr>
				</thead>
				<tbody>
			<?php 
				$Group='';
				foreach($ArrData as $w) {
					$DepN = array_keys($ArrDep,$w['department'])[0];
					If ($Group !=$w['department']) {
						$Group=$w['department'];
						if (
							isset($w['department2']) && (
							(mb_stripos ($w['department'],'управление')!==false) || 
							(Trim(mb_substr($w['department2'],0,mb_stripos($w['department2'],'|@|')-1)) == mb_substr($w['department2'],mb_stripos ($w['department2'],'|@|')+3,500))  ) 
							) {
							Echo '	<tr id="Group_'.$DepN.'" class="danger bs-docs-section">
										<th colspan=7>'
												.$w['department'].
										'</th>
									</tr>';/**/
						}
						Else {
							Echo '	<tr id="Group_'.$DepN.'" class ="info bs-docs-section"><th colspan=7>'.$w['department'];
							if ($w['displayname']===$w['department']) {
								Echo	'<br><br>'	.((!Empty($w['mail']))				?' Почтовый ящик: '.$w['mail'].					((!Empty($w['telephonenumber']))	?',  &nbsp;  &nbsp; ':'')	:((!Empty($w['telephonenumber']))	?',  &nbsp;  &nbsp; ':''))
													.((!Empty($w['telephonenumber']))	?' Городской телефон: '.$w['telephonenumber'].	((!Empty($w['ipphone']))			?',  &nbsp;  &nbsp; ':'')	:((!Empty($w['ipphone']))			?',  &nbsp;  &nbsp; ':''))
													.((!Empty($w['ipphone']))			?' IP телефон: '.$w['ipphone']																					:'');
							}
							Echo'</th></tr>';/**/
						}
					};
					if ($w['displayname']!==$w['department']) {
						if (empty($w['displayname'])) echo '<tr class ="colorgray">';
							else echo '<tr>';
						foreach($LDAPAttrShow['Param'] as $a) {
							if ( (isset($w[$a])) && ($w[$a]!='')){
								If ($a == 'displayname') {
									Echo '<td><a onclick="ShowUserModal(this)"  href="" data-toggle="modal" data-target="#modal-windows" data-text="pages/'.$PageCurent.'/details.php?ID='.urlencode(base64_encode(urlencode(utf8_encode(implode("||||", array($LDAPCurent,$UnitCurent,$w['objectguid'])))))).'"><span>'.
										SubStr($w[$a],0,StrPos($w[$a],' ')).'</span></br><span>'.SubStr($w[$a],StrPos($w[$a],' '),100).'</span></a></td>';
								}
								elseif ($a == 'mail')
									Echo '<td> <a href="mailto:'.$w[$a].'">'.$w[$a].'</a></td>';
									elseif (($a == 'telephonenumber') or ($a == 'pager') or ($a == 'ipphone'))
										Echo '<td>'.str_replace('/','<br>',$w[$a]).'</td>';
										else
											Echo '<td>'.$w[$a].'</td>';
							}
							Else
								Echo '<td>-</td>';
						};
						Echo '</tr>';
					}
				};
			?>
				</tbody>
			</table>
		<?php ?>
		
		<a href="#" id="goTop" class="btn btn-default " style="position: fixed; bottom: 20px; right: 20px; opacity: 1; cursor: pointer;">
			<span class="glyphicon glyphicon glyphicon-arrow-up"></span>
		</a>
		
		<script type="text/javascript">
			$('#goTop').click(function() {
				$('#content').animate({scrollTop: 0},500);
				return false;
			})
			
			var split_min = 200;
			var split_max = 3600;
			var split_mainmin =800;

			$('#split-bar').mousedown(function (e) {
				e.preventDefault();
				$(document).mousemove(function (e) {
					e.preventDefault();
					var x = e.pageX - $('#sidebar').offset().left;
					if (x > split_min && x < split_max && e.pageX < ($(window).width() - split_mainmin)) {  
						x=x-6;
						$('#sidebar').css("width", x);
					}
				})
			});
			$(document).mouseup(function (e) {
				document.cookie = "split="+($('#split-bar').offset().left-5);
				$(document).unbind('mousemove');
			});	
			
			function ShowUserModal(qrg) {
				$.ajax({
					type: 'POST',
					url: $(qrg).data("text"),
					beforeSend: function(){
						$("#modal-windows #modal-label").html('Информация о сотруднике');
						$("#modal-windows .modal-dialog").addClass('modal-lg');
						$("#result").html( 'Загрузка .....');
					},
					success: function(data){
						$( "#result" ).html( data );
					},
					error  : function(data){
						$( "#result" ).html( 'Ошибка загрузки');
					}
				});
			};
			$(window).resize(function () { 
				$('#DataUsers').css("margin-bottom", $("#content").height()-70);
		    }); 
			$('#DataUsers').css("margin-bottom", $("#content").height()-70);
		</script>
	</div>
</div>