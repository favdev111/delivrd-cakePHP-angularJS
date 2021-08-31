<?php
	$quady = 1;
	$quadx = 1;
	$style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => false,
    'hpadding' => 'auto',
    'vpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 10,
    'stretchtext' => 4
);
    $width = 20;
	$txt = "Ship to:\n";
    $this->Pdf->core->addPage('', 'A4');
	$this->Pdf->core->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));

	$wavenumber = $wave['Wave']['id'];
	$wavedate = $wave['Wave']['created'];
	$linecount = sizeof($wave['OrdersLine']);
	$productscount = sizeof($picklines);
	
	$this->Pdf->core->SetFont('helvetica', '', 24);
	$this->Pdf->core->MultiCell('', 5,"Wave Number", 0, 'L', 0, 0, 10, 15, true);
	$this->Pdf->core->MultiCell('', 5,$wavenumber, 0, 'L', 0, 0, 70, 15, true);

	$this->Pdf->core->write2DBarcode($wavenumber, 'QRCODE,M',10 , 30, 20, 20, $style, 'N');
	$this->Pdf->core->write1DBarcode($wavenumber, 'C128', 60, 30, '', 20, 0.4, $style, 'T');
   
	$this->Pdf->core->SetFont('helvetica', '', 18);
	$this->Pdf->core->MultiCell(100, '',"Number of products to pick:", 0, 'L', 0, 0, 10, 60, true);
	$this->Pdf->core->MultiCell(10, '',$productscount, 0, 'L', 0, 0, 90, 60, true);

	$this->Pdf->core->SetFont('helvetica', '', 14);
	$this->Pdf->core->MultiCell('', '',"Product Name", 0, 'L', 0, 0, 10, 70, true);
	$this->Pdf->core->MultiCell('', '',"Pick Qty", 0, 'L', 0, 0, 90, 70, true);
	$this->Pdf->core->MultiCell('', '',"Bin", 0, 'L', 0, 0, 120, 70, true);
	$this->Pdf->core->SetFont('helvetica', '', 12);
	$xaxis = 80;
	foreach ($picklines as $pickline):
		$this->Pdf->core->MultiCell('', 16,$pickline['productname'], 'LTR', 'L', 0, 0, 10, $xaxis, true);
		$this->Pdf->core->MultiCell('', 16,$pickline['pickquantity'], 'LTR', 'L', 0, 0, 90, $xaxis, true);
		$this->Pdf->core->MultiCell('', 16,$pickline['bin'], 'LTR', 'L', 0, 0, 120, $xaxis, true);
		$this->Pdf->core->Image($pickline['imageurl'] ,160, $xaxis, 16, 16, 'JPG', '', 'L', true, 300, '','','','LTR');
		$xaxis += 16;
	endforeach;
	$pdfname = "pickislip_".$wavenumber.".pdf";
	 $this->Pdf->core->Output($pdfname, 'D');
	
	
	
	
?>
