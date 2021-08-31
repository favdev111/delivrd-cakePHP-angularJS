<?php $this->AjaxValidation->active(); 
  $btnclass = 'btn btn green';

  if($availableinv < $sourcereorderpoint)
    $btnclass = 'btn yellow-crusta';
  if($availableinv < $sourcesafetystock)
    $btnclass = 'btn btn-danger';
?>

<div class="modal-content">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Issue/Receive <?= $current_inv['Warehouse']['name']; ?> <i class="fa fa-angle-right"></i> <?= $current_inv['Product']['name']; ?></h4>
    </div>

    <?php echo $this->Form->create('Inventory', array('class' => 'form-horizontal form-row-seperated','id' => 'count-inv')); ?>
    <div class="modal-body">
        <?php echo $this->Session->flash(); ?>

        <div class="row">
            <div class="col-md-4">
                <div class="portlet box blue">
                    <div class="portlet-title">                         
                        <div class="caption">
                            <i class="fa fa-camera"></i> Product Image
                        </div>
                    </div>
                    <div class="portlet-body">
                        <img src="<?php echo h($current_inv['Product']['imageurl']); ?>"  class="productImage img-responsive" rel="product_img" data-id="<?php echo $current_inv['Inventory']['product_id']; ?>">
                    </div>
                </div>
                <?php if ($this->Session->read('showvariants') == 1) { ?>         
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-camera"></i> Product Color &amp; Size
                        </div>
                    </div>
                    <div class="portlet-body">
                        <?php if(!empty($current_inv['Product']['color_id'])) 
                        echo "<li class='list-group-item' style='color:#".$current_inv['Product']['Color']['htmlcode'].";background-color:#".$current_inv['Product']['Color']['htmlcode']."'>".$current_inv['Product']['Color']['name']."<span class='badge'>".$current_inv['Product']['Color']['name']."</span></li>"; ?>
                        
                        <?php if(!empty($current_inv['Product']['size_id'])) 
                        echo "<li class='list-group-item'>".$current_inv['Product']['Size']['description']." <span class='badge'>".$current_inv['Product']['Size']['name']."</span></li>"; ?>
                    </div>
                </div>
                
                <?php } ?>
            </div>

            <div class="col-md-8">
                <?php #echo $this->element('sql_dump');?>
                <?php echo $this->Form->input('id', array( 'hidden' => true )); ?>
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-barcode"></i> <?php echo h($current_inv['Product']['name']) ?>
                        </div>
                    </div>
                    
                    <div class="portlet-body">
                        <?php if ($this->Session->read('locationsactive') == 1) { ?>
                        <div class="form-group">
                            <label class="col-md-4 control-label">Location: 
                            </label>
                            <div class="col-md-4">
                                <?php echo $this->Form->input('warehouse_id',array('label' => false, 'class' => 'form-control','div' =>false , 'id' => 'dmq','readonly' => true)); ?>
                            </div>
                        </div>  
                        <?php } ?>  
                    
                        <div class="form-group">
                            <label class="col-md-4 control-label">Available Qty.: 
                            </label>
                            <div class="col-md-4">
                                <?php echo "<button type='button' class='".$btnclass."'>".$availableinv."</button>" ?>
                            </div>
                        </div>
                                    
                        <div class="form-group">
                            <label class="col-md-4 control-label">Transaction: 
                                <span class="required">* </span>
                            </label>
                            <div class="col-md-4">
                                <?php echo $this->Form->input('ttype',array('label' => false,'class' => 'form-control select2me','id' => 'grgi','div' =>false, 'options' => array('GR' => 'Receive To Inventory' ,'GI' => 'Issue From Inventory'), 'required' => 'true', 'empty' => 'Select...')); ?>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="col-md-4 control-label">Quantity: 
                                <span class="required">* </span>
                            </label>
                            <div class="col-md-4">
                                <?php echo $this->Form->input('tquantity',array('label' => false, 'class' => 'form-control','div' =>false, 'id' => 'tqty','min' => '0','type' => 'number','step' => '1', 'required' => 'true')); ?>
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
    <div class="modal-footer">
        <button type="button" id="closeBtn" class="btn btn-default" data-dismiss="modal">Close</button>
        <button class="btn green" type="submit" id="clicksave"><i class="fa fa-check"></i> Save</button>
    </div>
    <?php echo $this->Form->end(); ?>
</div>
<script>
    $('#count-inv').submit(function() {
        $btn = $('#clicksave').button('loading');
    });
</script>
