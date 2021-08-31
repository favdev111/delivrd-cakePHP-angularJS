<?php
	$style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => true,
    'hpadding' => 'auto',
    'vpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 8,
    'stretchtext' => 4
);
	// $txt = "Ship to:\n";
	$this->Pdf->core->setPrintHeader(false);
	$this->Pdf->core->SetTopMargin(20);
    $this->Pdf->core->addPage('', 'A4');
	
	//$this->Pdf->core->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	//$this->Pdf->core->Rect($quadx, $quady, 105, $quady);
//	$this->Pdf->core->SetY($quady);
//	$this->Pdf->core->SetX($quadx);
    $this->Pdf->core->setFont('helvetica', '', 12);
	$currentbin = 100;
	$ydelta = 40;
	$binscount = $this->session->read('Auth.User.binscount');
	for($x=0;$x<(ceil($binscount / 3));$x++)
	{
	$yaxis = $x * $ydelta + 10;
	$this->Pdf->core->MultiCell(55, 5,'Bin No.'.$currentbin, 0, 'C', 0, 0, '5', $yaxis, true);
	$this->Pdf->core->write1DBarcode($currentbin, 'I25', '10', $yaxis + 5, '', 18, 0.4, $style, 'M');
	$currentbin++;
	$this->Pdf->core->MultiCell(55, 5,'Bin No.'.$currentbin, 0, 'C', 0, 0, '75', $yaxis, true);
	$this->Pdf->core->write1DBarcode($currentbin, 'I25', '80', $yaxis + 5, '', 18, 0.4, $style, 'M');
	$currentbin++;
	$this->Pdf->core->MultiCell(55, 5,'Bin No.'.$currentbin, 0, 'C', 0, 0, '155',$yaxis, true);
	$this->Pdf->core->write1DBarcode($currentbin, 'I25', '165', $yaxis + 5, '', 18, 0.4, $style, 'M');
	$currentbin++;

	}
	$this->Pdf->core->Output('binslabel.pdf', 'D');
	
	
	
?>