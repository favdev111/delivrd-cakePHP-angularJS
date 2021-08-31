<?php $this->AjaxValidation->active(); ?>
<div class="grid_2">
	<div class="box">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Form->postLink(__('UnDelete'), array('action' => 'undelete', $this->Form->value('Product.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('Product.id'))); ?></li>
		<li><?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $this->Form->value('Product.id')), array(), __('Are you sure you want to delete # %s?', $this->Form->value('Product.id'))); ?></li>
		<li><?php echo $this->Form->postLink(__('Print Product Label'), array('controller' => 'pdfs', 'action' => 'productlabel', $this->Form->value('Product.id'))); ?></li>
	</ul>
</div>
</div>
<div class="grid_10">
<?php echo $this->Form->create('Product'); ?>
	<fieldset>
		<legend><?php echo __('Edit Product'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo '<div class="viewbox">';
		echo '<h2>Description</h2>';
		echo $this->Form->label('name','Product Name','label');
		echo $this->Form->input('name',array('label' => false));
		echo $this->Form->label('description','Long Description','label');
		echo $this->Form->input('description',array('label' => false,'style'=>'width:500px;'));
		echo '</div>';
		echo '<div class="viewbox">';
		echo '<h2>Dimensions</h2>';
		echo "<table>";
		echo "<thead>";
		echo "<tr>";
		echo "<th>".$this->Form->label('weight','Weight','label')."</th>";
		echo "<th>".$this->Form->label('length','Length','label')."</th>";
		echo "<th>".$this->Form->label('height','Height','label')."</th>";
		echo "<th>".$this->Form->label('width','Width','label')."</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tr>";
		echo "<td>".$this->Form->input('weight',array('label' => false,'after' => $this->Session->read('weight_unit')))."</td>";
		echo "<td>".$this->Form->input('length',array('label' => false,'after' => $this->Session->read('volume_unit')))."</td>";
		echo "<td>".$this->Form->input('height',array('label' => false,'after' => $this->Session->read('volume_unit')))."</td>";
		echo "<td>".$this->Form->input('width',array('label' => false,'after' => $this->Session->read('volume_unit')))."</td>";
		echo "</tr>";
		echo "</table>";
		echo '</div>';
		echo '<div class="viewbox">';
		echo '<h2>Identification</h2>';
		echo "<table>";
		echo "<thead>";
		echo "<tr>";
		echo "<th>".$this->Form->label('barcode','EAN/UPC/ISBN','label')."</th>";
		echo "<th>".$this->Form->label('barcode_standards_id','Barcode standard','label')."</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tr>";
		echo "<td>".$this->Form->input('barcode',array('label' => false, 'after' => '12 or 13 digits'))."</td>";
		echo "<td>".$this->Form->input('barcode_standards_id',array('label' => false, 'options' => array('EAN' => 'EAN','UPC' => 'UPC','ISBN' => 'ISBN'),'empty' => '(choose one)'))."</td>";	
		echo "</tr>";
		echo "</table>";
		echo $this->Form->label('sku','SKU','label');
		echo $this->Form->input('sku',array('label' => false,'after' => '*'));
		echo "<a class='toggle'>Suggest SKU</a>";
		echo "<div class='block' id='filter'>";
		echo $suggestedsku;
		echo "</div>";
		echo '<div class="viewbox">';
		echo '<h2>Packaging</h2>';
		echo $this->Form->label('packaging_material_id','Packaging Material','label');
		echo $this->Form->input('packaging_material_id',  array('options' => $packmaterialsarr,'label' => false));
		echo $this->Form->label('packaging_instructions','Packaging Instructions','label');
		echo $this->Form->input('packaging_instructions',array('label' => false));
		echo $this->Form->label('consumption','Shipping Packaging Material','label');
		echo $this->Form->input('consumption',array('label' => false));
		echo '</div>';
		
		echo '<div class="viewbox">';
		echo '<h2>Links</h2>';
		echo $this->Form->label('imageurl','Product Image URL','label');
		echo $this->Form->input('imageurl',array('label' => false,'style'=>'width:500px;'));
		echo $this->Form->label('ebay_itemlist_url','Product Page URL','label');
		echo $this->Form->input('ebay_itemlist_url',array('label' => false,'style'=>'width:500px;'));
		echo '</div>';
		echo '<div class="viewbox">';
		echo '<h2>Attributes</h2>';
		echo "<table>";
		echo "<thead>";
		echo "<tr>";
		echo "<th>".$this->Form->label('color_id','Color','label')."</th>";
		echo "<th>".$this->Form->label('size_id','Size','label')."</th>";
		echo "</tr>";
		echo "</thead>";
		echo "<tr>";
		echo "<td>".$this->Form->input('color_id',array('label' => false))."</td>";
		echo "<td>".$this->Form->input('size_id', array('label' => false))."</td>";	
		echo "</tr>";
		echo "</table>";
		echo '</div>';	
		echo '<div class="viewbox">';
		echo '<h2>General Properties</h2>';
		echo $this->Form->label('group_id','Product Value','label');
		echo $this->Form->input('value',array('label' => false, 'after' => $this->Session->read('currencyname')));
		echo $this->Form->label('group_id','Product Category','label');
		echo $this->Form->input('group_id',array('label' => false));
		echo $this->Form->label('bin','Bin Number','label');
		echo $this->Form->input('bin',array('label' => false));
		echo $this->Form->label('safety_stock','Safety Stock','label');
		echo $this->Form->input('safety_stock',array('label' => false));
		echo '</div>';
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<script>
	$( "#filter" ).hide();
$(".toggle").click(function() {
  $( "#filter" ).slideToggle( "slow" );
});
</script>