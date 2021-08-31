<?php
	$quady = 1;
	$quadx = 1;
	
	$barstyle = array(
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

	
	$filename = "shipment-label-".$shipment['Shipment']['tracking_number'].".pdf";
	$width = 100;
	$xaxis = 3;
	$to = "To:\n\t";
        $returnto = "Return to:\n\t";
	$details = '';
	$twodurl = $userdetails['User']['userpage'];
	$this->Pdf->core->setPrintHeader(false);
	$this->Pdf->core->setPrintFooter(false);
    $this->Pdf->core->addPage('', 'A4');
	$this->Pdf->core->SetLineStyle(array('width' => 0.05, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
//REM

    $this->Pdf->core->setFont('helvetica', '', 16);
    $returnto = $returnto.$returnaddr['name']."\n\t";
	$returnto = $returnto.$returnaddr['street']."\n\t";
    $returnto = $returnto.$returnaddr['city']."\n\t";
    $returnto = $returnto.$returnaddr['country']."\n\t";
	
	$to = $to.$order['Order']['ship_to_customerid']."\n\t";
	$to = $to. (!empty($toroder['street']) ? (!empty($toroder['zip']) ? $toroder['street']. ", " . $toroder['zip'] : $toroder['street']) : $toroder['zip']) . "\n\t";
	$to = $to.$toroder['city']."\n\t";
	$to = $to. (!empty($toroder['stateprovince']) ? (!empty($toroder['country']) ? $toroder['stateprovince'] . ", " . $toroder['country'] : $toroder['stateprovince']) : $toroder['country']) . "\n\t"; 
	
	
	$char_enc = mb_detect_encoding($order['Order']['ship_to_street']);


	$country = $order['Country']['name']."\n\t";
	$ref_order = $order['Order']['external_orderid'];
	$details=$details."Ref. Order No.: ".$order['Order']['external_orderid']."\n";
	
	$tracking = $shipment['Shipment']['tracking_number'];

	if($order['Country']['code'] == 'IL')
	{
		$country = "ישראל"."\n";
	} 
	
	if(strlen($country) > 18)
	{
		$countryfontsize = 12;
	} else {
		$countryfontsize = 16;
	}
	for($c=0;$c<2;$c++)
	{
		
	$reford_offset = 30 - strlen($ref_order);
	$xoffset = $c*105;
	$this->Pdf->core->setFont('helvetica', '', 14);
	if($char_enc == 'UTF-8' || $order['Country']['code'] == 'RU' || $order['Country']['code'] == 'IL')
	{
		$this->Pdf->core->setFontSubsetting(true);
		$this->Pdf->core->SetFont('freeserif', '', 16);
		//Remove \t, they cause a mess
		$to = str_replace("\t", '', $to);
		$country = str_replace("\t", '', $country);
		$this->Pdf->core->MultiCell($width, 5,$to, 'LTR', 'L', 0, 0, ($xaxis+$xoffset), 3, true);
		$this->Pdf->core->SetFont('freeserif', '', $countryfontsize);
		$this->Pdf->core->MultiCell($width, 20,$country, 'LTR', 'L', 0, 0, ($xaxis+$xoffset), 47, true,0,false,true,30);
	} else {
        $this->Pdf->core->MultiCell($width, 5, $returnto, 'LTR', 'L', 0, 0, ($xaxis+$xoffset), 3, true);
        $this->Pdf->core->MultiCell($width, 5, $to, 'LTR', 'L', 0, 0, ($xaxis+$xoffset), 36, true);
		$this->Pdf->core->SetFont('times', '', $countryfontsize);
		$this->Pdf->core->MultiCell($width, 20, $country, 'LTR', 'L', 0, 0, ($xaxis+$xoffset), 70, true,0,false,true,30);
	}
	$this->Pdf->core->SetFont('helvetica', '', 10);
	$this->Pdf->core->MultiCell($width, 10, $details, 1, 'L', 0, 0, ($xaxis+$xoffset), 79, true);
	$this->Pdf->core->MultiCell($width, 10,'My Store',0, 'L', 0, 0, ($xaxis+$xoffset + 82), 30, true);
	$this->Pdf->core->write2DBarcode($twodurl, 'QRCODE,M', ($xaxis+$xoffset + 80), 3, 20, 20, $barstyle, 'N');
	$this->Pdf->core->write1DBarcode($ref_order, 'C128', ($xaxis+$xoffset + $reford_offset), 95, '', 20, 0.4, $barstyle, 'T');
	$this->Pdf->core->write1DBarcode($tracking, 'C128', ($xaxis+$xoffset + 15), 118, '', 20, 0.4, $barstyle, 'T');
    
	}
	$this->Pdf->core->Output($filename, 'D');
	
?>