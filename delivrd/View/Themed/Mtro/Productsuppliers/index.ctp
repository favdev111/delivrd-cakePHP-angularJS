<?php
    $this->AjaxValidation->active(); 
    $action_color = ($this->Session->read('locationsactive') == 1 ? 'green' : 'grey-salt'); // returns true
?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        
        <!-- BEGIN PAGE HEADER-->
        <?php echo $this->element('expirytext'); ?>

        <div class="page-bar">
            <?php echo $this->Form->create('Productsupplier', array(
                'class' => 'form-horizontal',
                'novalidate' => true,
                'url' => ['controller' => 'productsuppliers', 'action' => 'index'],
                'id' => 'productsupplier_form',
            ));   
                              
            $search = (!empty($this->request->data['Productsupplier']['searchby'])) ? $this->request->data['Productsupplier']['searchby'] : '';?> 
            <div class="row">
                <div class="col-md-4">
                    <?php echo $this->Form->input('searchby', array('label' => false, 'class'=>'code-scan form-control', 'placeholder' => 'Search by product name, sku', 'value' => $search, 'id' => 'autocomplete', 'style' => 'width: 370px;height: 32px;')); ?>
                </div>
                <div class="col-md-3">
                    <?php echo $this->Form->input('supplier_id', array('label' => false,'class'=>'form-control form-filter input-md','required' => false,'empty' => 'Supplier...', 'style' => 'width: 140px;height: 32px;')); ?>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-md blue filter-submit margin-bottom" type="submit" id="clicksearch"><i class="fa fa-search"></i></button>
                    <?php echo $this->html->link('<i class="fa fa-undo"></i>', array('plugin' => false, 'controller' => 'productsuppliers', 'action' => 'index'), array('class' => 'btn btn-md blue filter-submit margin-bottom', 'escape' => false)); ?>
                </div>
                <div class="col-md-1">

                </div>
                <div class="col-md-1">
                <?php echo $this->Html->link(__('<i class="fa fa-plus"></i>'), array('controller' => 'productsuppliers', 'action' => 'add'), array('class' => 'btn default yellow-stripe', 'escape' => false, 'title' => 'New Product-Supplier')); ?>
                </div>
                <div class="col-md-1">
                    <div class="page-toolbar">
                        <div class="btn-group pull-right">
                            <button type="button" class="btn btn-fit-height <?php echo $action_color; ?> dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                            <i class="fa fa-ellipsis-h"></i>
                            </button>
                            <ul class="dropdown-menu pull-right" role="menu">
                              <li><?php echo $this->Html->link(__('<i class="fa fa-file-excel-o"></i> Import Product-Supplier Assignment Data'), array('controller'=> 'productsuppliers','action' => 'uploadcsv'),array('escape'=> false)); ?></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <?php  echo $this->Form->end(); ?>
        </div>
        <!-- END PAGE HEADER-->

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <!-- Begin: life time stats -->
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-exchange"></i>Product-Supplier Assignment
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
                                        <th width="20%">Product</th>
                                        <th width="20%">Supplier</th>
                                        <th width="20%">Part Num.</th>
                                        <th width="20%">Created</th>
                                        <th width="20%">Status</th>
                                        <th width="15%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if(count($data) !== 0) { ?>
                                    <?php foreach ($data as $listOne) { ?>
                                    <tr>
                                        <td><?php echo h($listOne['Product']['name']); ?></td>
                                        <td><?php echo h($listOne['Supplier']['name']); ?></td>
                                        <td><?php echo h($listOne['Productsupplier']['part_number']); ?></td>
                                        <td><?php echo date("F j, Y",strtotime($listOne['Productsupplier']['created']));?></td>
                                        <td><?php echo ($listOne['Productsupplier']['status'] == 'yes') ? 'Active':'Inactive'; ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a class="btn btn-sm default yellow-stripe dropdown-toggle" href="#" data-toggle="dropdown"><i class="fa fa-ellipsis-h"></i></a>
                                                <ul class="dropdown-menu pull-right">
                                                    <li><?php echo $this->Html->link(__('<i class="fa fa-edit"></i>Edit'), array('controller' => 'productsuppliers', 'action' => 'edit', 'id' => $listOne['Productsupplier']['id']), array('escape' => false)); ?></li>
                                                    <li><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $listOne['Productsupplier']['id']), array('escape'=> false), __('Are you sure you want to this?')); ?></li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?> 
                                <?php } else { ?>
                                    <tr><td align='center' colspan='8'><b>No Data Found</b></td></tr>
                                <?php } ?>
                                </tbody>
                            </table>
                            <?php echo $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'))); ?>
                            <div>
                                <ul class="pagination">
                                    <?php
                                        $paginator = $this->Paginator;
                                        echo $paginator->first("First",array('tag' => 'li'));
                                        if($paginator->hasPrev()){
                                            echo $paginator->prev("Prev", array('tag' => 'li'));
                                        }
                                        echo $paginator->numbers(array('modulus' => 2,'tag' => 'li','currentTag' => 'a','currentClass' => 'active','separator' => false));
                                        if($paginator->hasNext()){
                                            echo $paginator->next("Next",array('tag' => 'li'));
                                        }
                                        echo $paginator->last("Last",array('tag' => 'li'));
                                    ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->