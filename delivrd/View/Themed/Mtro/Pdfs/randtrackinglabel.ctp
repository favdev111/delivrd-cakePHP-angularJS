<?php
	$quady = 20;
	$quadx = 20;
	$style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'cellfitalign' => '',
    'border' => false,
    'hpadding' => 'auto',
	'printheader' => 'false',
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
    'border' => true,
    'hpadding' => 'auto',
	'printheader' => 'false',
	'printfooter' => 'false',
    'vpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 8,
 //   'stretchtext' => 4
);
	// $txt = "Ship to:\n";
	
	$this->Pdf->core->setPrintHeader(false);
	$this->Pdf->core->setPrintFooter(false);
    $this->Pdf->core->addPage('', 'A4');
    $this->Pdf->core->SetAutoPageBreak(false);
	
	//$this->Pdf->core->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	//$this->Pdf->core->Rect($quadx, $quady, 105, $quady);
//	$this->Pdf->core->SetY($quady);
//	$this->Pdf->core->SetX($quadx);
    $this->Pdf->core->setFont('helvetica', '', 12);
    
	//$this->Pdf->core->write1DBarcode($sku_barcode, 'C128', 1, 1 , 25, 18, 0.4, $barstyle, 'M');
	$yoffset = 37;
	$xoffset = 70;
for($y=0;$y<8;$y++)
{
	if($y==0)
			$y = 0.2;
	$yvalue = $y * $yoffset ;
	for($x=0;$x<3;$x++)
	{
	 if($x==0)
			$x = 0.2;
	$xvalue = $x * $xoffset ;

	$tracking_number = substr(str_shuffle(MD5(microtime())), 0, 10);
		$this->Pdf->core->write1DBarcode($tracking_number, 'C128', $xvalue, $yvalue , 40, 20, 0.4, $barstyle, 'M');
	}
}
	//}	

	//}
	
	$filename = 'random_tracking_labels.pdf';
	
	$this->Pdf->core->Output($filename, 'D');
	
	
	
?>
