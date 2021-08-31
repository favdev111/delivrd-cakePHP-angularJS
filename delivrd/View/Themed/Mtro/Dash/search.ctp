
	<!-- BEGIN CONTENT -->
	<div class="page-content-wrapper">
		<div class="page-content">
			
			<!-- BEGIN PAGE HEADER-->
			<h3 class="page-title">
				Dashboard <small>search results</small>
			</h3>
			<!-- END PAGE HEADER-->

			<!-- BEGIN DASHBOARD STATS -->
			<div class="row">
				<div class="table-responsive">
									<table class="table table-advance table-hover">
									<thead>
									<tr>
										<th>
											 #
										</th>
										<th>
											<i class="fa fa-key"></i> Id
										</th>
										<th>
											<i class="fa fa-file"></i> Type
										</th>										
										<th>
											<i class="fa fa-info"></i> Result
										</th>
										<th>
											<i class="fa fa-calendar"></i> Created
										</th>
										<th>
											<i class="fa fa-bolt"></i> Action
										</th>
									</tr>
									</thead>
									<tbody>
									
								<?php 
								if(count($results) > 0){

									foreach ($results as $key=>$result):
									
										echo "<tr><td>".($key+1)."</td>";
										echo "<td>".$result['id']."</td>";
										echo "<td>".$result['type']."</td>";
										echo "<td>".$result['res']."</td>";										
										echo "<td>".$result['created']."</td>";							
										echo '<td><a href="'.$result['url'].'"class="btn btn-xs default btn-editable">';
										echo '<i class="fa fa-search"></i>';
										echo 'View';
										echo '</a></td></tr>';
									endforeach; 
								} else {
										echo "<td colspan='6'class='text-center'>No item found</td>";
									}?>
									
									</tbody>
									</table>
		</div>
	</div>

	<!-- END CONTENT -->
<div class="clearfix">
			</div>
</div>
</div>
<!-- END CONTAINER -->



