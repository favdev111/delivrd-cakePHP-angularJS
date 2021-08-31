<h4>Hello, <?php echo $this->App->username($product['User']); ?></h4>

<p>
	Product name: <?php echo $product['Product']['name']; ?><br>
	Product SKU: <?php echo $product['Product']['sku']; ?><br>
	Issue Quantity: <?php echo $issuedQuant; ?><br>
	Inventory quantity after issue: <?php echo $product['Inventory']['quantity']; ?><br>
	Reorder Point: <?php echo intval($product['Product']['reorder_point']); ?><br>
	Safety Stock: <?php echo intval($product['Product']['safety_stock']); ?><br>
</p>