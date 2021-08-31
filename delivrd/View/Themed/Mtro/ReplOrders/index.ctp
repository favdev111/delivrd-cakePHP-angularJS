<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="ReplOrderList">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>
            
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-random"></i> Purchase Orders List
                        </div>
                        <div class="actions">
                            <?php if($is_write) { ?>
                                <?php echo $this->Html->link(__('<i class="fa fa-plus"></i> Add Purchase Order'),
                                    array('plugin' => false, 'controller' => 'replorders', 'action' => 'create'),
                                    array('class' => 'btn default yellow-stripe add-delivrd statlink', 'escape' => false, 'title' => 'New Order', 'data-link' => 'new_po'));
                                ?>
                            <?php } ?>

                            <div class="btn-group">
                                <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <?php if($this->Session->read('showebaylink') == true && $is_write) { ?>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-cloud-download"></i> Import Ebay Orders'), array('controller' => 'ebay','action' => 'getEbayOrders'),array('escape'=> false)); ?></li>
                                    <?php } ?>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-ban"></i> Canceled Orders'), array('controller' => 'replorders', 'action' => 'canceled'), array('escape' => false)); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <?php if($is_have_access) { ?>
                    <div class="portlet-body">
                        <div class="csv-div">
                            <div class="btn-toolbar">
                                <div class="btn-group">
                                    <?php echo $this->Html->link(__('<i class="fa fa-download"></i> Export'), array('controller'=> 'orders','action' => 'exportcsv', 2),array('escape'=> false, 'class' => 'csv-icons', 'title' => 'Export inventory list')); ?>
                                    <?php if($is_write) { ?>
                                    <?php echo $this->Html->link(__('<i class="fa fa-upload"></i> Import'), array('controller'=> 'replorders','action' => 'uploadcsv'),array('escape'=> false, 'class' => 'csv-icons import-btn', 'title' => 'Import inventory from csv')); ?>
                                    <?php } ?>
                                </div>
                                <div class="btn-group pageLimit">
                                    <div class="form-inline">
                                        <div class="form-group">
                                            <label><i class="fa fa-list"></i> Results:</label>
                                            <?php echo $this->Form->select('pageBottom', $options, array(
                                                'value'=>$limit,
                                                'default' => 10,
                                                'empty' => false,
                                                'class'=>'form-control form-filter input-md limit'
                                                )
                                            ); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <?php echo $this->html->link('<i class="fa fa-undo"></i> Show All', array('plugin' => false, 'controller' => 'replorders', '?' => ['search' => 1]), array('class' => 'csv-icons import-btn', 'escape' => false)); ?>
                                </div>
                            </div>
                        </div>

                        <?php echo $this->Form->create('Order', array('class' => 'form-horizontal', 'url' => array_merge(array('controller'=>'replorders', 'action' => 'index'), $this->params['pass']))); ?>
                            <?php echo $this->Form->hidden('limit', ['value' => $limit]); ?>
                            <?php echo $this->Form->hidden('search', ['value' => 1]); ?>
                            <div class="row margin-bottom-20">
                                <div class="col-md-5">
                                    <div class="input-group col-md-10">
                                        <?php echo $this->Form->input('searchby', array('label' => false, 'autofocus' => 'autofocus', 'class'=>'form-control input-md', 'placeholder' => 'Search by Order #, Ref. # or Cust. name', 'value' => $this->request->query('searchby'))); ?>  
                                        <span class="input-group-addon"><button id="keysearch" class="" title="Search" style="border:none"><i class="fa fa-search"></i></button></span>
                                    </div>
                                </div>

                                <label class="col-md-1 control-label">Filter By: </label>
                                <div class="col-md-2">
                                    <?php echo $this->Form->input('supplier_id', array('label' => false,'class'=>'form-control form-filter input','required' => false, 'empty' => 'Supplier...','value'=>$this->request->query('supplier_id') )); ?>
                                </div>

                                <div class="col-md-3">
                                    <?php echo $this->Form->input('status_id',array('label' => false, 'class'=>'form-control form-filter input','value' => $po_filter, 'options' => array(14 => 'Draft', 2 => 'Released',3 => 'Ship. Proc.', 4 => 'Completed', 8 => 'Shipped', 55=> 'Paid'), 'multiple'=>true, 'empty' => 'Status...')); ?>
                                </div>

                                <div class="col-md-1 text-right">
                                    <button class="btn btn-md blue filter-submit margin-bottom" type="submit"><i class="fa fa-search"></i></button>
                                </div>
                            </div>
                        <?php  echo $this->Form->end(); ?>

                        <div id="multiFunctions" class="row margin-bottom-20 hide">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-fit-height dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-v"></i> With selected 
                                </button>
                                <ul class="dropdown-menu" role="menu">
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-lock"></i> Release Selected Orders'), array(), array('escape'=> false, 'id' => 'release-all-order')); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-ban"></i> Cancel Selected Orders'), array(), array('escape'=> false, 'ng-click' => '$event.preventDefault(); cancelMultiple();')); ?></li>
                                    <li><?php echo $this->Html->link(__('<i class="fa fa-trash"></i> Delete Selected Orders'), array(), array('escape'=> false, 'id' => 'trashOrders')); ?></li>
                                </ul>
                            </div>
                        </div>

                        <div class="table-container">
                            <table class="table table-hover" id="datatable_orders">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th>#</th>
                                        <th>Order&nbsp;#</th>
                                        <th>Supplier</th>
                                        <th>Reference Order</th>
                                        <th><i class="fa fa-sort"></i> <?php echo $this->Paginator->sort('created'); ?></th>
                                        <th>Status</th>
                                        <th width="140px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $key => $order) { ?>
                                    <tr role="row" class="filter" id="checkbox_row_<?php echo h($order['Order']['id']); ?>">
                                        <td><?php echo $this->Form->checkbox('Order.id.' . $key, array('class' => 'checkboxes', 'value' => $order['Order']['id'], 'hiddenField' => false)); ?></td>
                                        <td><?php echo h($order['Order']['id']); ?></td>
                                        <td>
                                            <?php echo h($order['Supplier']['name']); ?>
                                            <?php echo h($order['Supplysource']['name']); ?>
                                        </td>
                                        <td><?php echo h($order['Order']['external_orderid']); ?>&nbsp;</td>
                                        <td><?php echo $this->Admin->localTime("%B %e, %Y", strtotime($order['Order']['created']));?></td>
                                        <td><?php echo $this->Order->status($order['Order']['status_id']); ?></td>
                                        <?php
                                            if(isset($this->params['url']['av'])) {
                                                if($order['State']['id'] != 1) {
                                                    $addr = $order['Order']['ship_to_street'].",".$order['Order']['ship_to_city'].",".$order['State']['name'].",".$order['Order']['ship_to_zip'].",".$order['Country']['name'];
                                                } else {
                                                    $addr = $order['Order']['ship_to_street'].",".$order['Order']['ship_to_city'].",".$order['Order']['ship_to_zip'].",".$order['Country']['name'];
                                                }
                                                echo "<td>
                                                    <iframe width=\"250\" height=\"75\" frameborder=\"0\" style=\"border:0\" src=\"https://www.google.com/maps/embed/v1/search?key=AIzaSyCUNb1khtmBzVwLRUmKWzK4jD-tYMe_dbY&q=".h($addr)."\">
                                                    </iframe>
                                                    </td>";
                                            }
                                        ?>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <?php if($order['Order']['status_id'] == 14 && ($this->Session->read('Auth.User.id') == $order['Order']['user_id'] || strpos($order[0]['access'], 'w') !== false)) { ?>
                                                    <?php if($popup == 1) {  ?>
                                                        <?php echo $this->Html->link(__('Release'),
                                                            array('controller' => 'replorders', 'action' => 'release',$order['Order']['id']),
                                                            array('escape'=> false, 'class'=>'btn btn-xs blue')
                                                        ); ?>
                                                    <?php } else { ?>
                                                        <a href="#" class="release-id btn btn-xs blue" id="<?php echo $order['Order']['id']; ?>" data-toggle="modal" data-href1="<?php echo Router::url(array('action' => 'details', $order['Order']['id']), true); ?>" data-href2="<?php echo Router::url(array('controller' => 'replorders', 'action' => 'release', $order['Order']['id']), true); ?>" data-target="#release-modal"> Release</a>
                                                    <?php } ?>
                                                <?php } ?>

                                                <?php if(($order['Order']['status_id'] == 2 || $order['Order']['status_id'] == 3) && ($this->Session->read('Auth.User.id') == $order['Order']['user_id'] || strpos($order[0]['access'], 'w') !== false)) { ?>
                                                    <?php echo $this->Html->link(__('<i class="fa fa-arrow-right"></i>Receive'), array('controller' => 'orders_lines', 'action' => 'receive',$order['Order']['id']), array('escape'=> false, 'class'=>'btn btn-xs default')); ?>
                                                <?php } ?>

                                                <?php if($order['Order']['status_id'] == 4 && ($this->Session->read('Auth.User.id') == $order['Order']['user_id'] || strpos($order[0]['access'], 'w') !== false)) { ?>
                                                    <?php #echo $this->Html->link(__(''), array('controller' => 'replorders', 'action' => 'paid', $order['Order']['id']), array('data-toggle' => 'modal', 'data-target'=>'#ajaxModal', 'escape'=> false, 'class'=>'btn btn-xs grey-salsa')); ?>
                                                    <a href ng-click="makePaid(<?php echo $order['Order']['id']; ?>)" class="btn btn-xs grey-salsa"><i class="fa fa-credit-card"></i> Paid</a>
                                                <?php } ?>

                                                <a class="dropdown-toggle delivrd-act" href="#" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-h"></i>
                                                </a>
                                                <ul class="dropdown-menu pull-right" role="menu">
                                                    <li><?php echo $this->Html->link('<i class="fa fa-search"></i> Details',array('action' => 'details', $order['Order']['id']), array('escape' => false)); ?></li>
                                                    <?php if(($order['Order']['status_id'] == 2 || $order['Order']['status_id'] == 3) && ($this->Session->read('Auth.User.id') == $order['Order']['user_id'] || strpos($order[0]['access'], 'w') !== false)) { ?>
                                                        <?php if( ($this->Session->read('is_admin') == 3 || $this->Session->read('is_admin') == 1) && $this->Session->read('Auth.User.id') == $order['Order']['user_id']) { ?>
                                                        <li><?php echo $this->Html->link(__('<i class="fa fa-truck"></i> Create Shipment'), array('controller' => 'shipments', 'action' => 'add',$order['Order']['id'],0),array('escape'=> false)); ?></li>
                                                        <?php } ?>
                                                        <li><?php echo $this->Html->link(__('<i class="fa fa-undo"></i> Cancel Order Release'), array('action' => 'unrelease',$order['Order']['id']), array('escape'=> false)); ?></li>
                                                    <?php } ?>
                                                    <?php if(($order['Order']['status_id'] == 2 || $order['Order']['status_id'] == 3 || $order['Order']['status_id'] == 8) && ($this->Session->read('Auth.User.id') == $order['Order']['user_id'] || strpos($order[0]['access'], 'w') !== false)) { ?>
                                                        <li><?php echo $this->Html->link(__('<i class="fa fa-flag-checkered"></i> Complete'), array('controller' => 'orders','action' => 'complete', $order['Order']['id'], '?' => array('nonang' => 1)), array('escape' => false, 'data-toggle' => 'modal', 'data-target'=>'#ajaxModal')); ?></li>
                                                    <?php } ?>
                                                    <?php if ($order['Order']['status_id'] == 14 && ($this->Session->read('Auth.User.id') == $order['Order']['user_id'] || strpos($order[0]['access'], 'w') !== false)) { ?>
                                                        <li><?php echo $this->Html->link(__('<i class="fa fa-pencil"></i> Edit Order'), array('controller' => 'replorders', 'action' => 'edit', $order['Order']['id']),array('escape' => false)); ?></li>
                                                        <li><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $order['Order']['id']), array('escape'=> false), __('Are you sure you want to delete order # %s?', $order['Order']['id'])); ?></li>
                                                    <?php } ?>
                                                    <?php /*if($order['Order']['status_id'] == 2) { ?>
                                                        <li><a href>Shipped By supplier</a></li>
                                                    <?php }*/ ?>
                                                    <li>
                                                        <a href ng-click="addDocument(<?php echo $order['Order']['id']; ?>)"><i class="fa fa-upload"></i> Documents</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                            
                            <?php echo $this->Paginator->counter(array('format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}'))); ?>
                            <div class="btn-toolbar">
                                <div class="btn-group">
                                    <ul class="pagination">
                                        <?php
                                            $paginator = $this->Paginator;
                                            echo $paginator->first("First",array('tag' => 'li'));
                                            if($paginator->hasPrev()) {
                                                echo $paginator->prev("Prev", array('tag' => 'li'));
                                            }
                                            echo $paginator->numbers(array('modulus' => 2,'tag' => 'li','currentTag' => 'a','currentClass' => 'active','separator' => false));
                                            if($paginator->hasNext()) {
                                                echo $paginator->next("Next",array('tag' => 'li'));
                                            }
                                            echo $paginator->last("Last",array('tag' => 'li'));
                                        ?>
                                        <li></li>
                                    </ul>
                                </div>
                                <div class="btn-group pageLimit">
                                    <div class="form-inline">
                                        <div class="form-group">
                                            <label><i class="fa fa-list"></i> Results:</label>
                                            <?php echo $this->Form->select('pageBottom', $options, array(
                                                'value'=>$limit,
                                                'default' => 10,
                                                'empty' => false,
                                                'class'=>'form-control form-filter input-md limit'
                                                )
                                            ); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="bs-callout bs-callout-info">
                                        <span class="font-blue-steel help">
                                            <i class="fa fa-info" aria-hidden="true"></i>
                                            <a href="https://delivrd.freshdesk.com/support/solutions/articles/17000013488-managing-purchase-orders-in-delivrd" target="_blank">Managing Purchase Orders in Delivrd Tutorials</a>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php } else { ?>
                    <div class="portlet-body">
                        <div class="alert alert-danger">
                            <p class="lead text-center">
                                You do not have access to purchase orders.
                                <?php if(!$authUser['is_limited']) { ?>
                                <?php echo $this->Html->link(
                                        __('Start trial'),
                                        array('plugin' => false, 'controller' => 'user', 'action' => 'start_trial', '?' => ['type' => 'po']),
                                        array('style' => 'display:none', 'id'=>'startSoTrial', 'escape' => false, 'data-toggle' => 'modal', 'data-target' => '#ajaxModal')
                                    );
                                ?>
                                <?php } ?>
                            </p>
                        </div>
                    </div>
                    <?php } ?>


                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<div class="modal fade" id="release-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body release-btns">
                <p>Would you like to view order details before releasing order ?</p>
                <a href="" class="btn btn-md blue" id="review-wave">Review order</a>
                <a href="" class="btn btn-md blue" id="release">Release</a>
                <div class="row" style="margin-top: 13px;">
                    <div class="col-md-2 value">
                        <input type="checkbox" name="show_message" id="show_message" />
                    </div>
                    <div class="col-md-6 name" style="margin-left: -46px;">
                        <p>Don't show this message again</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="wave-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Create Wave</h4>
            </div>
            <?php echo $this->Form->create('Wave', array('url' => array('controller' => 'waves', 'action' => 'createWave'), 'class' => 'form-horizontal', 'id' => 'createWaveForm')); ?>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-md-3"><label class="control-label">Courier: </label></div>
                    <div class="col-md-8">
                        <?php echo $this->Form->input('courier_id',array( 'label' => false, 'class' => 'form-control input-sm select2me','empty' => 'Select...' )); ?>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-3"><label class="control-label">Resources: </label></div>
                    <div class="col-md-8">
                        <?php  echo $this->Form->input('resource_id',array( 'label' => false, 'class' => 'form-control input-sm select2me','empty' => 'Select...' )); ?>
                        <?php  echo $this->Form->hidden('order_id',array( 'label' => false, 'id' => 'selected_orders')); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-md blue">Save</button>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>

<?php
    echo $this->Form->create('Order', array(
        'type' => 'post',
        'id' => 'checkbox_form',
        'url' => array_merge(
                array(
                    'action' => 'release_multiple'
                ),
                $this->params['pass']
            ),
        'class' => 'form-horizontal list_data_form',
        'novalidate' => true,
    )); 
    echo $this->Form->hidden('id', array('id' => 'order_id')); 
    echo $this->Form->end();
?>

<?php echo $this->Form->create('Order', array(
        'type' => 'post',
        'id' => 'delete_form',
        'url' => array_merge(
                array(
                    'action' => 'delete_multiple'
                ),
                $this->params['pass']
            ),
        'class' => 'form-horizontal list_data_form',
        'novalidate' => true,
    ));
    echo $this->Form->hidden('order_id',array( 'label' => false, 'id' => 'selected_ids'));
    echo $this->Form->end();
?>

<script type="text/javascript">
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    
    var doc_title = 'Purchase Order';
    
    $('.limit').change(function(){
        $('#OrderLimit').val($(this).val());
        $('#OrderIndexForm').submit();
    });

    $(document).ready(function() {
        <?php if(!$is_have_access) { ?>
            $('#startSoTrial').trigger('click');
        <?php } ?>
    });

    $('#trashOrders').click(function(e){
        e.preventDefault();
        var checkedVals = $('.checkboxes:checkbox:checked').map(function() {
            return this.value;
        }).get();

        if(checkedVals == '') {
            toastr.error('Please select orders', "", {tapToDismiss: false, closeButton:true, closeHtml: '<button><i class="fa fa-close"></i></button>',timeOut: false,})
        } else {
            $("#selected_ids").val(checkedVals.join(","));
            //console.log(checkedVals);
            $('#delete_form').submit();
        }
    });
    
    $('.checkboxes').click(function(){
        var checkedVals = $('.checkboxes:checkbox:checked').map(function() {
            return this.value;
        }).get();
        //console.log(checkedVals); //
        $('tr.filter').each(function(){
            if($(this).find('input.checkboxes').is(':checked')) {
                $(this).addClass('bg-grey-steel bg-font-grey-steel');
            } else {
                $(this).removeClass('bg-grey-steel').removeClass('bg-font-grey-steel');
            }
        });
        if(checkedVals != '') {
            $('#OrderIndexForm').hide();
            $('#multiFunctions').removeClass('hide').show();
        } else {
            $('#OrderIndexForm').show();
            $('#multiFunctions').hide();
        }
    });

    $('.limit').select2({
        minimumResultsForSearch: -1,
        width: '80px'
    });

    $('#OrderSupplierId').select2({
        placeholder: 'Select Supplier',
        minimumResultsForSearch: -1
    });

    $('#OrderStatusId').select2({
        placeholder: 'Select..',
        minimumResultsForSearch: -1
    });
<?php $this->Html->scriptEnd(); ?>
</script>

<?php echo $this->Html->script('/app/ReplOrders/index.js?v=0.0.6', array('block' => 'pageBlock')); ?>
<?php echo $this->Html->script('/app/Documents/view.js?v=0.0.2', array('block' => 'pageBlock')); ?>