<td>
<?php $add = ''; 
	  $count = 0; 
   foreach($listOne as $suppliers) { 
   	if($suppliers['status'] == 'yes') {
     $add .= '<li>' .(!empty($suppliers['Supplier']['name']) ? $suppliers['Supplier']['name'] : ''). '</li>';
     $count = $count + 1;
   	}

   } ?>
	<div id="<?php echo $id; ?>" class="hide">
		<ul>
		 	<?php echo h($add); ?>
		</ul>
	</div>

<?php if($count > 1) : ?>
<a href="javascript:void(0)" rel="popover" id="supplier-pop" data-popover-content="#<?php echo $id; ?>"><i class="icon-plus"></i></a>
<?php endif;  

foreach ($listOne as $key => $suppliers):
	if($suppliers['status'] == 'yes') {
	  echo (!empty($suppliers['Supplier']['name']) ? $suppliers['Supplier']['name'] : '');
	  break;
	}
endforeach; ?>
  

</td>     