<?php $this->AjaxValidation->active(); ?>
<?php
    $addlinetext = (isset($addpack) == true ? "Add Packaging Material" : "Add Order Line");
?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">

        <!-- BEGIN PAGE HEADER-->
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a href="/">Home</a>
                </li>
            </ul>
            <div class="page-toolbar">
                <div class="btn-group pull-right">
                    <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
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
                                $title = 'sales';

                            } else {

                                $actiontext = "editrord";
                                $icon = "fa-random";
                                $pagecolor = "green";
                                $title = 'replenishment';
                            }

                            //if(sizeof($currentlines) > 0)
                                echo $this->Html->link(__('<i class="fa fa-unlock"></i> Release'), array('controller' => 'orders', 'action' => 'release',$currentOrder['Order']['id']), array('escape'=> false)); ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <?php echo $this->Form->input('orderid', array('label' => false,'id' => 'order_id', 'type' => 'hidden','value' => $this->request->query['ordid']));
            echo $this->Form->hidden('title', array('label' => false,'id' => 'title','value' => $title));
            echo $this->Form->hidden('copypdtprice', array('label' => false,'id' => 'copypdtprice','value' => $this->Session->read('copypdtprice'))); ?>
        <!-- END PAGE HEADER-->

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
            <?php echo $this->Session->flash(); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <!-- Begin: life time stats -->
                <?php echo $this->Form->create('OrdersLine', array('id' => 'OrdersLine', 'onsubmit'=> "save_order_live(event);", 'class' => 'form-horizontal form-row-seperated')); ?>
                    <div class="portlet box <?php echo $pagecolor; ?>">
                        <div class="portlet-title">
                            <div class="caption">
                                <i class="fa <?php echo $icon; ?>"></i><?php echo $addlinetext ?><span class="hidden-480">
                                </span>
                            </div>
                            <div class="actions">
                                <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                                <button class="btn green" ><i class="fa fa-check"></i> Save</button>
                            </div>
                        </div>

                        <div class="portlet-body">
                            <div class="table-scrollable">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><label class="control-label">Product <span class="required">*</span></label></th>
                                            <th><label class="control-label">Qty. <span class="required">*</span></label></th>
                                            <th><label class="control-label"><?php echo "Unit Price (".$currencyname.")"; ?></label></th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <?php  echo $this->Form->input('product_id',array('label' => false,'data-placeholder' => 'Select...','class' => 'form-control input-large select2me', 'id' => 'select_product_id','div' =>false, 'empty' => 'Select...', 'required')); ?>
                                            </td>
                                            <td>
                                                <?php echo $this->Form->input('quantity',array('label' => false, 'class' => 'form-control input-large','div' =>false, 'min' => 1, 'required')); ?>
                                            </td>
                                            <td>
                                                <?php echo $this->Form->input('unit_price', array('label' => false, 'class' => 'form-control','div' =>false)); ?>
                                            </td>
                                            <td>
                                                <?php echo $this->Form->input('comments',array('label' => false,'class' => 'form-control','div' =>false)); ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-hover" id="datatable_orders">
            <thead>
                <tr role="row" class="heading">
                    <th><?php echo "Order Number"; ?></th>
                    <th><?php echo "Line Number"; ?></th>
                    <th><?php echo "Description"; ?></th>
                    <th><?php echo "Quantity"; ?></th>
                    <th><?php echo "Unit Price (".$currencyname.")"; ?></th>
                    <th><?php echo "Line Total (".$currencyname.")"; ?></th>
                    <th><?php echo "Notes"; ?></th>
                    <th class="actions"><?php echo __('Actions'); ?></th>
                </tr>
            </thead>
            <tbody id="tablediv">
            </tbody>
        </table>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->
