<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            <?php echo $this->element('expirytext'); ?>
            
            <?php if($this->Session->read('showtours') == 1) { ?>
            <div class="portlet light bordered">
                <div class="portlet-title">
                    <div class="caption">
                        <i class="fa graduation-cap"></i>Inventory Page Tour</div>
                    <div class="tools">
                        <a href="javascript:;" class="collapse"> </a>
                        <a href="/users/edit" class="config"> </a>
                        <a href="javascript:;" class="reload"> </a>
                        <a href="javascript:;" class="remove"> </a>
                    </div>
                </div>
                <div class="portlet-body">
                    <div id="takeTheTour" class="btn btn red dismissable">Take Page Tour</div>  
                  If you want to disable page tour, <a href='/users/edit/'><U>change your settings</U></a>.
                </div>
            </div>
            <?php } ?>
            
            <!-- BEGIN PAGE CONTENT-->
            <?php echo $this->Session->flash(); ?>
            <div class="row">
            <div class="col-md-6">
                    <div class="portlet box blue">
                        <div class="portlet-title">                         
                            <div class="caption">
                            <i class="fa fa-camera"></i>
                            Product Image
                            </div>
                        </div>
                        <div class="portlet-body"> 
                        <?php echo '<img src='.$current_inv['Product']['imageurl']." height='128px' width='128px' >"; ?>
                        
                        </div>
                    </div>
                    
                </div>
            <?php if ($this->Session->read('showvariants') == 1) { ?>
            <div class="col-md-4">
                    <div class="portlet box blue">
                        <div class="portlet-title">
                            <div class="caption">
                            <i class="fa fa-camera"></i>
                            Product Color &amp; Size
                            </div>
                        </div>
                        <div class="portlet-body">
                            <?php if(!empty($product['Product']['color_id'])) 
                            echo "<li class='list-group-item' style='color:#".$product['Product']['Color']['htmlcode'].";background-color:#".$product['Product']['Color']['htmlcode']."'>".$product['Product']['Color']['name']."<span class='badge'>".$product['Product']['Color']['name']."</span></li>"; ?>
                            <?php if(!empty($product['Product']['size_id'])) 
                            echo "<li class='list-group-item'>".$product['Product']['Size']['description']." <span class='badge'>".$product['Product']['Size']['name']."</span></li>"; ?>
                        </div>
                    </div>
                    
                </div>
                <?php } ?>
            </div>
        
            <div class="row">
                <div class="col-md-6">
                <?php echo $this->Form->create('Inventory', array('class' => 'form-horizontal form-row-seperated', 'novalidate' => true, 'id' => 'count-inv'));
                echo $this->Form->input('id',array( 'hidden' => true ));
            
                 ?>
                    
                
                        <div class="portlet box blue">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-barcode"></i> <?php echo h($current_inv['Product']['name']) ?>
                                </div>
                                <div class="actions btn-set">
                                    <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>

                                    <script>
                                    function goBack() {
                                        window.history.back()
                                    }
                                    </script>   
                                    <button class="btn green" type="submit" id='clicksave'><i class="fa fa-check"></i> Save</button>

                                </div>
                            </div>
                        
                            <div class="portlet-body">
                                <?php if ($this->Session->read('locationsactive') == 1) { ?>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Location:</label>
                                    <div class="col-md-4">
                                        <?php echo $this->Form->input('warehouse_id',array('label' => false, 'class' => 'form-control','div' =>false , 'id' => 'dmq','readonly' => true)); ?>
                                    </div>
                                </div>  
                                <?php } ?>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">Available Stock: <span class="required">*</span></label>
                                    <div class="col-md-4">
                                        <?php echo $this->Form->input('quantity',array('label' => false, 'class' => 'form-control','div' =>false, 'id' => 'avq', 'min' => '0','type' => 'number','step' => '1')); ?>
                                    </div>
                                </div>
                            
                                <?php if ($this->Session->read('managedamaged') == 1) { ?>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Damaged Quantity: <span class="required">*</span></label>
                                    <div class="col-md-4">
                                        <?php echo $this->Form->input('damaged_qty',array('label' => false, 'class' => 'form-control','div' =>false , 'id' => 'dmq', 'min' => '0','type' => 'number','step' => '0.01')); ?>
                                    </div>
                                </div>  
                                <?php } ?>
                                                
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Remarks: </label>
                                    <div class="col-md-8">
                                        <?php echo $this->Form->input('comments',array('label' => false, 'class' => 'form-control','div' =>false , 'id' => 'cremarks')); ?>
                                    </div>
                                </div>
                                    
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->
<ol id="joyRideTipContent">
    <li data-id="avq" data-button="Next" data-options="tipLocation:right">
        <h2>Available Stock</h2>
        <p>Enter quantity of available (not damaged) stock you counted.</p>
    </li>
    <li data-id="dmq" data-button="Next" data-options="tipLocation:right">
        <h2>Enter damaged stock quantity</h2>
        <p>If you have damaged pieces in stock, enter their quantity here.</p>
    </li>
    <li data-id="cremarks" data-button="Next" data-options="tipLocation:right">
        <h2>Remarks</h2>
        <p>Type any remark you have for this product's inventory count. Remarks show up in Transactions History.</p>
    </li>
    <li data-id="clicksave" data-button="Next" data-options="tipLocation:right">
        <h2>Save</h2>
        <p>Click the Save button to update this product's inventory quantity.</p>
    </li>
</ol>
