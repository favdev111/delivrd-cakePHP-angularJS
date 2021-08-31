<?php $this->AjaxValidation->active(); ?>
<!-- BEGIN CONTENT -->
    <div class="page-content-wrapper" ng-controller="SerialEdit">
        <div class="page-content">
            <?php echo $this->element('expirytext'); ?>
        
            <!-- BEGIN PAGE CONTENT-->

            <?php echo $this->Session->flash(); ?>
            <div class="row">
                <div class="col-md-3">
                    <div class="portlet box blue-chambray">
                        <div class="portlet-title">
                            <div class="caption">
                            <i class="fa fa-camera"></i>
                            Product Image
                            </div>
                        </div>
                        <div class="portlet-body">
                        <?php echo '<img src='.h($productimageurl)." height='256px' width='256px' >"; ?>    
                        
                        </div>
                    </div>
                </div>

                <div class="col-md-9">
                    <?php echo $this->Form->create('Serial', array('class' => 'form-horizontal form-row-seperated')); 
                    
                    echo $this->Form->input('id',array('hidden' => true));?>
                        <div class="portlet box blue-chambray">
                            <div class="portlet-title">
                                <div class="caption">
                                    <i class="fa fa-barcode"></i><?php echo "Serial Number ".h($this->Form->value('Serial.serialnumber')).", Product ".h($this->Form->value('Product.name')) ?>
                                </div>
                                <div class="actions btn-set">
                                    <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                                    <button class="btn green" type="submit"><i class="fa fa-check"></i> Save</button>
                                    <a href ng-click="addDocument(<?php echo $serial['Serial']['id']; ?>, '<?php echo $serial['Serial']['serialnumber']; ?>')" class="btn btn-fit-height blue"><i class="fa fa-upload"></i> Documents</a>
                                </div>
                            </div>
                            <div class="portlet-body">
                                
                                    <div class="tab-content no-space">
                                        <div class="tab-pane active" id="tab_general">
                                            <div class="form-body">
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">In Stock: 
                                                    </label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('instock',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Purchase Order: </label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('order_id_in',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <label class="col-md-2 control-label">Customer Order: 
                                                    </label>
                                                    <div class="col-md-10">
                                                        <?php echo $this->Form->input('order_id_out',array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                                        
                                                    </div>
                                                </div>
                                                
                                            </div>
                                        </div>
                                            </td>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        
            <div class="row">
                            
                
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
    <!-- END CONTENT -->

<script type="text/javascript">
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    
    var doc_title = 'Serial #';

<?php $this->Html->scriptEnd(); ?>
</script>

<?php echo $this->Html->script('/app/Serials/edit.js?v=0.0.2', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/app/Documents/view.js?v=0.0.2', array('block' => 'pageBlock')); ?>