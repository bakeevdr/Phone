<?php
require_once('prepare.php');
require_once "../../library/fpdf.php";
define('FPDF_FONTPATH', '../../fonts/');
$Title = 'Телефонный справочник ('.$P_LDAP[$LDAPCurent]['Name'].")";

class TelPhone extends FPDF
{
	var $Title_; 
	var $LDAPAttrShow_; 
	var $WidthPage=831;

	function initialization(){
		Global $Title;
		$this->Title_ = $Title;
		Global $LDAPAttrShow;
		$this->LDAPAttrShow_ = $LDAPAttrShow;
 		$Width_Sum = array_sum($this->LDAPAttrShow_['PDF_W']);
		foreach($this->LDAPAttrShow_['PDF_W'] as $i=>$q)
			$this->LDAPAttrShow_['PDF_W'][$i] = round($this->WidthPage/$Width_Sum*$q);
		$this->WidthPage = array_sum($this->LDAPAttrShow_['PDF_W']);
		$this->SetCreator('Выгрузка из LDAP',True);
		$this->SetAuthor('Бакеев Дамир Рустамович' ,True);
		$this->SetTitle($this->Title_,True);
		$this->SetSubject('Телефонный справочник',True);
		$this->AddFont('Tahoma','','tahoma.php');
		$this->AddFont('Tahoma-Bold','','tahoma_bold.php');
		$this->AliasNbPages();
		$this->SetDisplayMode('fullpage','continuous');
		$this->SetMargins(5,5);
		$this->SetAutoPageBreak(true,10);
	}
	
	function Header(){
		$this->SetFont('Tahoma-Bold','','14');
		$this->Image('../../image/logo_kp.png',5,2,28); 
		$this->Cell(0,22,iconv('utf-8', 'windows-1251',$this->Title_),0,0,'C');
		$this->SetFont('Tahoma','','9');
		$this->Text(680,30,iconv('utf-8', 'windows-1251', 'Дата формирования: ').date("d-m-Y") );
		$this->Ln(35);
		$this->SetFont('Tahoma-Bold','','9');
		$this->SetFillColor(173, 173, 173); 
		foreach($this->LDAPAttrShow_['PDF_W'] as $i=>$q) 
			$this->Cell($q,22,iconv('utf-8', 'windows-1251',$this->LDAPAttrShow_['Name'][$i]),1,0,'C',true);
		$this->Ln();
	}

	function Footer(){
		$this->SetY(-15);
		$this->SetFont('Tahoma','','9');
		$this->Cell(0,10,iconv('utf-8', 'windows-1251', 'Страница '.$this->PageNo().' из {nb}'),0,0,'C');
	}
	
	function TableData($Data){
		$this->SetFont('Tahoma','','9');
		$Group='';
		$Hcell = 25;
		$PB	=	-1;
		$this->AddPage();
		foreach($Data as $w) {
			$PB++;
			if (($PB % 20)==0 & ($PB!=0))
				$this->AddPage();
			
		$WLine	=	array();
			If ($Group !=$w['department']) {
				$Group=$w['department'];
				$this->SetFont('Tahoma-Bold','','9');
				$this->SetFillColor(196, 227, 243); 
				$this->Cell($this->WidthPage,$Hcell - 5,iconv('utf-8', 'windows-1251',$Group),1,0,'C',True);
				$this->Ln();
				$PB++;
				if (($PB % 20)==0 & ($PB!=0))
					$this->AddPage();
				$this->SetFont('Tahoma','','9');
			}
			foreach($this->LDAPAttrShow_['Param'] as $I=>$a) {
				if (isset($w[$a])) {
					$St_ = iconv('utf-8', 'windows-1251',$w[$a]);
					If (stripos($St_,' / ') === false) {
						If ($a == 'displayname') {
							$this->SetFont('Tahoma-Bold','','9');
							$this->Cell($this->LDAPAttrShow_['PDF_W'][$I],$Hcell/2,substr($St_,0,stripos($St_,' ')),'LRT',0,'L');
							$this->SetFont('Tahoma','','9');
							$WLine[$I] = substr($St_,stripos($St_,' ')+1,100);
						}
						Else {
							$cw = &$this->CurrentFont['cw'];
							$Wmax	=	($this->LDAPAttrShow_['PDF_W'][$I]-2*2.835)*1000/$this->FontSize;
							$l = 0;
							for($St_i=0;$St_i<strlen($St_);$St_i++){
								If ($St_[$St_i] ===' ')
									$Ln = $St_i;
								$l += $cw[$St_[$St_i]];
								if($l>$Wmax) {
									$this->Cell($this->LDAPAttrShow_['PDF_W'][$I],$Hcell/2,substr($St_,0,$Ln),'LRT',0,'C');
									$WLine[$I] = substr($St_,$Ln,200);
									break;
								}
							}
							if (!isset($WLine[$I]))
								$this->Cell($this->LDAPAttrShow_['PDF_W'][$I],$Hcell,$St_,'LRT',0,'C');
						}
					}
					else {
						$this->Cell($this->LDAPAttrShow_['PDF_W'][$I],$Hcell/2,substr($St_,0,stripos($St_,' / ')),'LRT',0,'C');
						$WLine[$I] = substr($St_,stripos($St_,' / ')+4,100);
					}
				}
				Else 
					$this->Cell($this->LDAPAttrShow_['PDF_W'][$I],$Hcell,'',1,0);
			}
			$this->SetXY(5,$this->GetY()+($Hcell/2));
			foreach($this->LDAPAttrShow_['Param'] as $I=> $a) {
				if (isset($WLine[$I]))
					If ($a == 'displayname') 
						$this->Cell($this->LDAPAttrShow_['PDF_W'][$I],$Hcell/2,'      '.$WLine[$I],'LRB',0,'L');
					Else
						$this->Cell($this->LDAPAttrShow_['PDF_W'][$I],$Hcell/2,$WLine[$I],'LRB',0,'C');
				Else 
					$this->Cell($this->LDAPAttrShow_['PDF_W'][$I],$Hcell/2,'','LRB',0,'C');
			}/**/
			$this->Ln();
		}
	}
}

$pdf=new TelPhone('L','pt','A4');
$pdf->initialization();
$pdf->TableData($ArrData);
$pdf->Output('TelPhone.pdf','D');
?>