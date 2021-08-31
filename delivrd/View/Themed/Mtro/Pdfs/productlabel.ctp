<?php
	$quady = 2;
	$quadx = 2;
	$style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => false,
    'hpadding' => 'auto',
	'printheader' => 'false',
    'vpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 8,
 //   'stretchtext' => 4
);

    $barstyle = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => false,
    'hpadding' => 'auto',
	'printheader' => 'false',
    'vpadding' => 'false',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 8,
 //   'stretchtext' => 4
);
	// $txt = "Ship to:\n";
	$sku_barcode = $product['Product']['sku'];	
	$this->Pdf->core->setPrintHeader(false);
    $this->Pdf->core->addPage('', 'A4');
	
	//$this->Pdf->core->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	//$this->Pdf->core->Rect($quadx, $quady, 105, $quady);
//	$this->Pdf->core->SetY($quady);
//	$this->Pdf->core->SetX($quadx);
    $this->Pdf->core->setFont('helvetica', '', 12);
	$currentbin = 100;
	$ydelta = 26;
	for($x=0;$x<(ceil(20 / 3));$x++)
	{
	$yaxis = $x * $ydelta + 10;
	
	$this->Pdf->core->write1DBarcode($sku_barcode, 'I25', '10', $yaxis + 5, '', 18, 0.4, $style, 'M');
	
	$this->Pdf->core->write1DBarcode($sku_barcode, 'I25', '60', $yaxis + 5, '', 18, 0.4, $style, 'M');
	
	$this->Pdf->core->write1DBarcode($sku_barcode, 'I25', '110', $yaxis + 5, '', 18, 0.4, $style, 'M');

	$this->Pdf->core->write1DBarcode($sku_barcode, 'I25', '160', $yaxis + 5, '', 18, 0.4, $style, 'M');
	

	}
	
	$this->Pdf->core->Output('productslabel.pdf', 'D');
	
	
	
?>