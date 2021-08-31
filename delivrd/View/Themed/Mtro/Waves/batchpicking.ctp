<?php #$this->AjaxValidation->active(); ?>
    <!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="BatchPick" ng-init = "showScreen(<?php echo $id; ?>, <?php echo $wave['Wave']['type']; ?>)" ng-cloak>
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>
        
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <div id="flash"></div>
                <div class="portlet box red-thunderbird" ng-show="IsVisible">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-barcode"></i>Products list
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="table-container">
                            <div class="table-actions-wrapper">
                                <span></span>
                                <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                            </div>
                            <table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th>Image</th>
                                        <th>Product Name</th>
                                        <th>Quantity</th>   
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product_list): ?>
                                    <tr role="row">
                                        <td><?php echo "<img src=".$product_list['Product']['imageurl']." height='32px' width='32px'>"; ?></td> 
                                        <td><?php echo h($product_list['Product']['name']); ?>&nbsp;</td>
                                        <td><?php echo h($product_list[0]['total_qty']); ?>&nbsp;</td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php #echo $this->Form->postLink(__('<i class="fa fa-print"></i> Print'), array('action' => 'pickingslip', $id), array('class' => 'btn btn-fit-height green', 'escape'=> false)); ?>
                
                <button type="button" class="btn btn-fit-height green" ng-click="showList()">
                    Products to Pick
                </button>
                <div style="margin: 30px 0px;" id="batch-pick-screen">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4 col-sm-12">
                            <div class="portlet yellow-crusta box">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-cogs"></i>Product to pack: <small></small>
                                    </div>
                                </div>
                                <div class="portlet-body">
                                    <div class="tiles">
                                        <div class="tile image selected">
                                            <div class="tile-body">
                                                <img src="{{count.imageurl}}" alt="">
                                            </div>
                                            <div class="tile-object">
                                            
                                            </div>
                                        </div>
                                        <h2>{{count.productname}}</h2>
                                        <h4 ng-if="count.bin" >Bin: {{count.bin}}</div>
                                    </div>
                                    
                                <?php if(!empty($pickline['color'])) 
                                    echo "<li class='list-group-item' style='color:".$pickline['colorhtml'].";background-color:#".$pickline['colorhtml']."'>".$pickline['color']."<span class='badge'>".$pickline['color']."</span></li>"; ?>
                                <?php if(!empty($pickline['size'])) 
                                    echo "<li class='list-group-item'>".$pickline['sizedescription']." <span class='badge'>".$pickline['size']."</span></li>"; ?>
                            
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 col-md-offset-4 col-lg-2 col-lg-offset-5 col-sm-8 col-sm-offset-2 ">
                            <?php

                                echo $this->Form->create('', array('id' => 'pickbyorder', 'ng-submit' => 'Pickbyorder($event, '.$wave['Wave']['type'].', '.$wave['Wave']['id']. ')')); 
                                echo $this->Form->hidden('locationid',array('value' => $wave['Wave']['location_id'], 'hidden' => true));
                                echo $this->Form->input('id',array('value' => '{{count.lineqty}}', 'hidden' => true));  
                                echo $this->Form->input('ordernumber',array('value' => '{{count.ordernumber}}', 'hidden' => true, 'label' => false));
                                echo $this->Form->input('productid',array('value' => '{{count.productid}}', 'hidden' => true, 'label' => false));
                                echo $this->Form->input('lineid',array('value' => '{{count.lineid}}', 'hidden' => true, 'label' => false));
                            ?>
                            <?php if($scan['User']['batch_pick'] == 1){
                                $disable = true;
                                $auto_focus = 'autofocus';
                            }   
                            else{
                                $disable = false;
                                $auto_focus = '';
                            } ?>
                            <table><tr>

                                <?php if($scan['User']['batch_pick'] == 2)
                                {
                                    echo '<div class="form-group form-md-line-input form-md-floating-label">';      
                                    echo $this->Form->input('scan',array('label' => false,'id' => 'track', 'disabled' => $disable, 'class' => 'form-control code-scan','div' => false));
                                    echo '<label for="form_control_1">Product barcode</label>';
                                    echo '<span class="help-block">Scan the product bin</span>';
                                    echo "</div>";
                                }
                                else if($scan['User']['batch_pick'] == 3)
                                {
                                    echo '<div class="form-group form-md-line-input form-md-floating-label">';      
                                    echo $this->Form->input('sku',array('label' => false,'id' => 'track', 'disabled' => $disable, 'class' => 'form-control code-scan','div' => false));
                                    echo '<label for="form_control_1">Product barcode</label>';
                                    echo '<span class="help-block">Scan the product SKU/EAN number</span>';
                                    echo "</div>";
                                }
                                elseif($scan['User']['batch_pick'] == 4)
                                {
                                    echo '<div class="form-group form-md-line-input form-md-floating-label">';      
                                    echo $this->Form->input('bin',array('label' => false,'id' => 'track', 'class' => 'form-control code-scan','div' => false));
                                    echo '<label for="form_control_1">Product BIN</label>';
                                    echo '<span class="help-block">Scan the products bin number</span>';
                                    echo "</div>";
                                    echo '<div class="form-group form-md-line-input form-md-floating-label">';      
                                    echo $this->Form->input('sku',array('label' => false,'id' => 'track1', 'class' => 'form-control code-scan','div' => false));
                                    echo '<label for="form_control_1">Product SKU</label>';
                                    echo '<span class="help-block">Scan the products sku number</span>';
                                    echo "</div>";
                                }

                                    echo '<div class="count-input space-bottom"><div class="form-group form-md-line-input form-md-floating-label">';
                                    echo '<a class="incr-btn" data-action="decrease" href="#">–</a>';   
                                    echo $this->Form->input('sentqty',array('value' => '{{count.sentqty}}','max' => '{{count.sentqty}}','label' => false,'id' => 'sentqty','disabled' =>false, 'class' => 'quantity form-control','div' => false, '' => '', $auto_focus));
                                    echo '<a class="incr-btn" data-action="increase" href="#">&plus;</a>';  
                                    echo '<span class="help-block">Pick Quantity</span>';
                                    echo "</div></div>";
                                
                                
                                    echo '<div class="form-group form-md-line-input form-md-floating-label text-center">';
                                        echo '<button class= "btn btn-fit-height green">SUBMIT</button>';
                                    echo "</div>";

                                
                                echo $this->Form->end(); ?>
                            </tr></table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->
