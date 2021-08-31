<?php $this->AjaxValidation->active();
    $btnclass = 'btn btn green';

    if($availableinv < $sourcereorderpoint)
        $btnclass = 'btn yellow-crusta';
    if($availableinv < $sourcesafetystock)
        $btnclass = 'btn btn-danger';
?>
<!-- BEGIN CONTENT -->
    <div class="page-content-wrapper">
        <div class="page-content">
            
            <?php echo $this->element('expirytext'); ?>
            
            <!-- BEGIN PAGE CONTENT-->
            
            <?php echo $this->Session->flash(); ?>
        
            <div class="row">
                <div class="col-md-6">
                    <?php echo $this->Form->create('Inventory', array('class' => 'form-horizontal form-row-seperated','id' => 'count-inv')); ?>
                        <div class="portlet box blue">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-ship"></i>Transfer Between Locations
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
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Product: 
                                    </label>
                                    <div class="col-md-8">
                                        <?php echo $this->Form->input('product_id',array('label' => false, 'class' => 'form-control','div' =>false, 'options' => $product, 'readonly' => true)); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-4 control-label">From Location: 
                                    </label>
                                    <div class="col-md-4">
                                        <?php echo $this->Form->input('warehouse_id_from',array('label' => false, 'class' => 'form-control','div' =>false , 'options' => $source_warehouse, 'readonly' => true)); ?>
                                    </div>
                                </div>  
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Available Qty.: 
                                    </label>
                                    <div class="col-md-4">
                                        <?php echo "<button type='button' class='".$btnclass."'>".$availableinv."</button>" ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-4 control-label">To Location: <span class="required">*</span>
                                    </label>
                                    <div class="col-md-4">
                                        <?php echo $this->Form->input('warehouse_id_to',array('label' => false, 'class' => 'form-control','div' =>false , 'options' => $warehouses2)); ?>
                                    </div>
                                </div>          
                        
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Transfer Quantity: <span class="required">*</span></label>
                                    <div class="col-md-4">
                                        <?php echo $this->Form->input('tquantity',array('label' => false, 'class' => 'form-control','div' =>false, 'id' => 'avq', 'min' => '0','type' => 'number','step' => '0.01','required' => 'true')); ?>
                                    </div>
                                </div>
                                    
                                <div class="form-group">
                                    <label class="col-md-4 control-label">Remarks: 
                                    </label>
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

<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    $('#InventoryWarehouseIdTo').select2({
        minimumResultsForSearch: -1
    });
<?php $this->Html->scriptEnd(); ?>