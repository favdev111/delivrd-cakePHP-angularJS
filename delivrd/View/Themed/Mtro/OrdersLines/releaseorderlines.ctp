<?php
// Only if we have order lines, we can release order, and color of action dropdown is green
 if(sizeof($currentlines) > 0)
 {
	$actiondropcolor = "green";
 } else {
	
	$actiondropcolor = "grey-salt";
 }

	$addlinetext = (isset($addpack) == true ? "Add Packaging Material" : "Add Order Line");
?>

<button type="button" class="btn btn-fit-height <?php echo $actiondropcolor; ?>   dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
		Actions <i class="fa fa-angle-down"></i>
		</button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li>
				<?php
			
				if($currentOrder['Order']['ordertype_id'] == 1)
				{
					$actiontext = "editcord";
					$icon = "fa-shopping-cart";
					$pagecolor = "red";
					
				} else {
					
					$actiontext = "editrord";
					$icon = "fa-random";
					$pagecolor = "green";
				}
				
		if(sizeof($currentlines) > 0)	
			echo $this->Html->link(__('<i class="fa fa-unlock"></i> Release'), array('controller' => 'orders', 'action' => 'release',$currentOrder['Order']['id']), array('escape'=> false)); 
						
		?>
			</li>
			
		</ul>