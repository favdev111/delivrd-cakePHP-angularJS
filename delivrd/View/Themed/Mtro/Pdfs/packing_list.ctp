<?php
	$quady = 145;
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
	$txt = "Ship to:\n";
    $this->Pdf->core->addPage('', 'A4');
	$this->Pdf->core->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
	$this->Pdf->core->Rect($quadx, $quady, 105, $quady);
    $this->Pdf->core->SetY($quady);
	$this->Pdf->core->SetX($quadx);
    $this->Pdf->core->setFont('helvetica', '', 12);
	$name = $order['Order']['ship_to_customerid'];
	$street = $order['Order']['ship_to_street'];
	$city = $order['Order']['ship_to_city'];
	$zip = $order['Order']['ship_to_zip'];
	$country = $order['Order']['country_id'];
	$external_order = $order['Order']['external_orderid'];
	$weight = $shipment['Shipment']['weight'];
	$tracking = $shipment['Shipment']['tracking_number'];

	$this->Pdf->core->cell($quadx, 0, $txt);
	$this->Pdf->core->Ln();
    $this->Pdf->core->cell($quadx, 0, $name);
	$this->Pdf->core->Ln();
	$this->Pdf->core->cell($quadx, 0, $street);
	$this->Pdf->core->Ln();
	$this->Pdf->core->cell($quadx, 0, ($zip." ".$city));
	$this->Pdf->core->Ln();
	$this->Pdf->core->SetFont('helvetica','B',14); 
	$this->Pdf->core->cell($quadx, 0, $country);
	$this->Pdf->core->Ln(22);
	$this->Pdf->core->Line(0, $quady + 40, 105,$quady + 40);
	$this->Pdf->core->setFont('helvetica', '', 12);
	$this->Pdf->core->cell(0, 0, ("Order Number: ".$external_order));
	$this->Pdf->core->Ln(8);
	$this->Pdf->core->cell(0, 0, ("Date: ".date("Y-m-d")));
	$this->Pdf->core->Ln(8);
	$this->Pdf->core->cell(0, 0, ("Weight: ".$weight));
	$this->Pdf->core->Line(0, $quady + 70, 105,$quady + 70);
	$this->Pdf->core->Ln(35);
	$this->Pdf->core->write1DBarcode($tracking, 'C128', '', '', '', 26, 0.4, $style, 'N');
    $this->Pdf->core->Output('shipmentlabel.pdf', 'D');
	
	
	
?>