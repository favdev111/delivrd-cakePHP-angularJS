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
	$details = '';
	$twodurl = $userdetails['User']['userpage'];
	$this->Pdf->core->setPrintHeader(false);
	$this->Pdf->core->setPrintFooter(false);
    $this->Pdf->core->addPage('', 'A4');
	$this->Pdf->core->SetLineStyle(array('width' => 0.05, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
//REM

    $this->Pdf->core->setFont('helvetica', '', 16);
	$to = $to.$order['Order']['ship_to_customerid']."\n\t";
	$to = $to.$order['Order']['ship_to_street']."\n\t";
	$to = $to.$order['Order']['ship_to_city']."\n\t";
	$to = $to.$order['Order']['ship_to_zip']."\n\t";
	if($order['State']['code'] != 'XZ' || $order['Order']['state_id'] != 99)
	{
		$to = $to.$order['State']['name']."\n\t";
	}
	$country = $order['Country']['name']."\n\t";
	$ref_order = $order['Order']['external_orderid'];
	$details=$details."Ref. Order No.: ".$order['Order']['external_orderid']."\n";
	$details=$details."Shipment Weight: ".$shipment['Shipment']['weight'].$weight_unit."\n";
	$details=$details."Estimated Value: ".$orderslines['OrdersLine']['total_line'].$currencyname."\n";
	$details=$details."Content: ".$categoryname."\n";
	$tracking = $shipment['Shipment']['tracking_number'];
	
	if(strlen($country) > 18)
	{
		$countryfontsize = 16;
	} else {
		$countryfontsize = 32;
	}
	for($c=0;$c<2;$c++)
	{
	$xoffset = $c*105;
	$this->Pdf->core->setFont('helvetica', '', 16);
	//$this->Pdf->core->setFontSubsetting(true);
	//$this->Pdf->core->SetFont('freeserif', '', 12);
    $this->Pdf->core->MultiCell($width, 5,$to, 'LTR', 'L', 0, 0, ($xaxis+$xoffset), 3, true);
	$this->Pdf->core->SetFont('times', '', $countryfontsize);
	$this->Pdf->core->MultiCell($width, 20,$country, 'LTR', 'L', 0, 0, ($xaxis+$xoffset), 47, true,0,false,true,30);
	$this->Pdf->core->SetFont('helvetica', '', 10);
	$this->Pdf->core->MultiCell($width, 10,$details, 1, 'L', 0, 0, ($xaxis+$xoffset), 60, true);
	$this->Pdf->core->MultiCell($width, 10,'My Store',0, 'L', 0, 0, ($xaxis+$xoffset + 82), 23, true);
	$this->Pdf->core->write2DBarcode($twodurl, 'QRCODE,M', ($xaxis+$xoffset + 80), 3, 20, 20, $barstyle, 'N');
	$this->Pdf->core->write1DBarcode($ref_order, 'C128', ($xaxis+$xoffset + 30), 85, '', 20, 0.4, $barstyle, 'T');
	$this->Pdf->core->write1DBarcode($tracking, 'C128', ($xaxis+$xoffset + 15), 110, '', 20, 0.4, $barstyle, 'T');
    
	}
	$this->Pdf->core->Output($filename, 'D');

	
	
	
?>
