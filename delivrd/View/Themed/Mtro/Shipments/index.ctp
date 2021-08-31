<?php 
    $base_url = explode('/', $this->here); 
    unset($base_url[0]);
    unset($base_url[1]);
    unset($base_url[2]);
    $base_url = implode('/', $base_url); 
if($direction == 1)
{
    $indexurl = "/shipments/index/1";                                   
    $pagetext = "Outbound Shipments";                       
    $pagecolor = "red-flamingo";                        
    $icon = "fa-space-shuttle";
} else {

    $indexurl = "/shipments/index/2";   
    $pagetext = "Inbound Shipments";
    $pagecolor = "green-jungle";
    $icon = "fa-plane";
}
?>
<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="ShipmentsList">
    <div class="page-content">
        <?php echo $this->element('expirytext'); ?>

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                    
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa <?php echo $icon; ?>"></i><?php echo $pagetext ?> List
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="csv-div">
                            <div class="btn-toolbar">
                                <div class="btn-group">
                                    <?php $exportIndex = ($direction == 1) ? 1 : 2;
                                        echo $this->Html->link(__('<i class="fa fa-download"></i> Export'), array('controller'=> 'shipments','action' => 'exportcsv', $exportIndex),array('escape'=> false, 'class' => 'csv-icons')); ?>
                                </div>
                                <div class="btn-group pageLimit">
                                    <div class="form-inline">
                                        <div class="form-group">
                                            <label><i class="fa fa-list"></i> Results:</label>
                                            <?php echo $this->Form->select('pageBottom', $options, array(
                                                'value' => $limit,
                                                'default' => 10,
                                                'empty' => false,
                                                'class'=>'form-control form-filter input-md limit',
                                                'ng-change' => 'applySearch()',
                                                'ng-model' => 'limit',
                                                //'select2' => ''
                                                )
                                            ); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-group">
                                    <a href class="csv-icons import-btn" title="Show all records" ng-click="$event.preventDefault(); showAll();"><i class="fa fa-undo"></i> Show All</a>
                                </div>
                            </div>
                        </div>
                        
                        <?php echo $this->Form->create('Shipment', array('class' => 'form-horizontal', 'url' => array_merge(array('action' => 'index'), $this->params['pass']))); ?>
                            <div class="row margin-bottom-20">
                                <div class="col-md-3">
                                    <?php echo $this->Form->input('tracking_number', array(
                                        'label' => false,
                                        'class'=>'form-control form-filter input-md',
                                        'required' => false,
                                        'autofocus' => true,
                                        'placeholder' => 'Tracking Number',
                                        'ng-model' => 'tracking_number',
                                        'ng-change' => "applySearch()"
                                    ));?>
                                </div>

                                <div class="col-md-2">
                                    <div class="input-group date date-picker margin-bottom-5" data-date-format="yyyy-mm-dd" data-date-orientation="right">
                                        <input datepickercustom
                                                type="text"
                                                class="form-control form-filter datepicker"
                                                placeholder="From"
                                                name="createdfrom"
                                                ng-change="applySearch()"
                                                ng-model="createdfrom"
                                            >
                                        <span class="input-group-btn" ng-click="setDateTime()">
                                            <button class="btn default" type="button"><i class="fa fa-calendar"></i></button>
                                        </span>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <?php echo $this->Form->input('status_id',array(
                                        'label' => false,
                                        'class'=>'form-control form-filter input-md',
                                        'options' => array(15 => 'Released', 6 => 'Ready for Shipment', 7 => 'Fully Received', 8 => 'Shipped',16 => 'Partially Processed'),
                                        'empty' => 'Status...',
                                        'ng-change' => 'applySearch()',
                                        'ng-model' => "status_id",
                                        'multiple' => true,
                                        
                                    )); ?>
                                </div>
                            
                                <div class="col-md-3">
                                    <div class="margin-bottom-5">
                                        <button class="btn btn-md blue-354fdc filter-submit margin-bottom" type="button" ng-click="applySearch()"><i class="fa fa-search"></i> Search</button>
                                    </div>
                                </div>
                            </div>
                        <?php  echo $this->Form->end(); ?>

                        <div class="table-container">
                            <div class="table-actions-wrapper">
                                <button class="btn btn-sm yellow table-group-action-submit"><i class="fa fa-check"></i> Submit</button>
                            </div>
                            <table class="table table-hover dataTable no-footer" id="datatable_products" aria-describedby="datatable_products_info" role="grid">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th>Id</th>
                                        <?php if($index != 1) echo "<th>Ext. Order#</th>" ?>
                                        <th>Courier</th>
                                        <th>Tracking Number</th>
                                        <th width="15%">
                                            <i class="fa fa-sort"></i>
                                            <?php echo $this->Paginator->sort('created') ?>
                                        </th>
                                        <th>Status</th>
                                        <th width="100px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr  ng-repeat="line in shipments | orderBy:sortType:sortReverse" id="line_{{line.Shipment.id}}" role="filter row">

                                        <td>{{line.Shipment.id}}</td>
                                        <td ng-if="direction_id != 1">
                                            <?php echo ang($this->Html->link('{{line.Order.external_orderid}}', array('controller' => 'orders', 'action' => 'details', '{{line.Shipment.id}}'))); ?>
                                        </td>
                                        <td>
                                            <?php /*if($authUser['id'] != $shipment['Order']['user_id']) { ?>
                                                <?php echo $networks[$shipment['Order']['user_id']]['name']; ?> &raquo;
                                            <?php }*/ ?>
                                            {{line.Courier.name}}
                                        </td>
                                        <td>{{line.Shipment.tracking_number}}</td>
                                        <td>
                                            <span ng-bind="formatDate(line.Shipment.created) | date:'MMMM dd, yyyy'"></span>
                                        </td>
                                        <td>
                                            <span ng-bind-html="status(line.Shipment.status_id)"></span>
                                            <span ng-if="line.Order.wave" class="label label-info">In Wave</span>
                                        </td>
                                        <td class="text-right">
                                            <div class="btn-group">
                                                <a class="delivrd-act dropdown-toggle" href="#" data-toggle="dropdown">
                                                    <i class="fa fa-ellipsis-h"></i>
                                                </a>
                                                <ul class="dropdown-menu pull-right">
                                                    <li><?php echo ang($this->Html->link(__('<i class="fa fa-search"></i> View'), array('plugin' => false, 'controller'=> 'shipments','action' => 'view', '{{line.Shipment.id}}'),array('escape'=> false))); ?></li>
                                                    <li ng-if="is_own(line)"><?php echo ang($this->Html->link(__('<i class="fa fa-pencil"></i> Edit'), array('plugin' => false, 'controller'=> 'shipments','action' => 'edit', '{{line.Shipment.id}}'),array('escape'=> false))); ?></li>
                                                    <li ng-if="is_own(line) && direction_id == 1">
                                                        <form action="<?php echo ang($this->Html->url(array('controller' => 'pdfs', 'action' => 'shiplabelee', '{{line.Shipment.id}}'))); ?>" style="display:none;" method="post"><input type="hidden" name="_method" value="POST"></form>
                                                        <a href="#" onclick="this.previousElementSibling.submit(); event.returnValue = false; return false;"><i class="fa fa-print"></i> Print Shipping Label</a>
                                                    </li>
                                                    <li ng-if="is_own(line) && direction_id == 1 && line.Order.wave && (line.Shipment.status_id == 15 || line.Shipment.status_id == 14 || line.Shipment.status_id == 16)"><?php echo ang($this->Html->link(__('<i class="fa fa-cube"></i> Complete Processing'), array('plugin' => false, 'controller'=> 'shipments','action' => 'changestatusp', '{{line.Shipment.id}}', 6), array('escape'=> false))); ?></li>
                                                    <li ng-if="is_own(line) && direction_id == 1 && !line.Order.wave && (line.Shipment.status_id == 15 || line.Shipment.status_id == 14 || line.Shipment.status_id == 16)"><?php echo ang($this->Html->link(__('<i class="fa fa-cube"></i> Complete Processing'), array('plugin' => false, 'controller'=> 'orders_lines','action' => 'issue', '{{line.Shipment.order_id}}'),array('escape'=> false))); ?></li>
                                                    <li ng-if="is_own(line) && direction_id == 1 && line.Shipment.status_id == 6 "><?php echo ang($this->Html->link(__('<i class="fa fa-truck"></i> Set Shipped'), array('plugin' => false, 'controller'=> 'shipments','action' => 'changestatusp', '{{line.Shipment.id}}', 8),array('escape'=> false))); ?></li>

                                                    <li ng-if="is_own(line) && direction_id != 1 && (line.Shipment.status_id == 15 || line.Shipment.status_id == 14)"><?php echo ang($this->Html->link(__('<i class="fa fa-cube"></i> Receive'), array('plugin' => false, 'controller'=> 'orders_lines','action' => 'receive', '{{line.Shipment.order_id}}'),array('escape'=> false))); ?></li>
                                                </ul>
                                            </div>

                                        </td>
                                    </tr>

                                    <?php /* foreach ($shipments as $shipment) { ?>
                                    <tr role="row">

                                    <td><?php echo h($shipment['Shipment']['id']); ?></td>
                                    <?php if($index != 1) { ?>
                                        <td><?php echo $this->Html->link(__(h($shipment['Order']['external_orderid'])), array('controller' => 'orders', 'action' => 'details', $shipment['Shipment']['order_id'])); ?></td>
                                    <?php } ?>
                                    <td>
                                        <?php if($authUser['id'] != $shipment['Order']['user_id']) { ?>
                                            <?php echo $networks[$shipment['Order']['user_id']]['name']; ?> &raquo;
                                        <?php } ?>
                                        <?php echo h($shipment['Courier']['name']); ?>
                                    </td>
                                    <td><?php echo $shipment['Shipment']['tracking_number']; ?> </td>
                                    <td>
                                    <?php echo $this->Admin->localTime("%B %d, %Y", strtotime($shipment['Shipment']['created'])); ?></td>
                                    <td><?php echo $this->Order->shipmentStatus($shipment); ?></td>
                                    <td class="text-right">
                                        <div class="btn-group">
                                            <a class="delivrd-act dropdown-toggle" href="#" data-toggle="dropdown">
                                                <i class="fa fa-ellipsis-h"></i>
                                            </a>
                                            <ul class="dropdown-menu pull-right">
                                                <li><?php echo $this->Html->link(__('<i class="fa fa-search"></i> View'), array('plugin' => false, 'controller'=> 'shipments','action' => 'view',$shipment['Shipment']['id']),array('escape'=> false)); ?></li>
                                                <?php if($authUser['id'] == $shipment['Shipment']['user_id']) { ?>
                                                    <li><?php echo $this->Html->link(__('<i class="fa fa-pencil"></i> Edit'), array('plugin' => false, 'controller'=> 'shipments','action' => 'edit',$shipment['Shipment']['id']),array('escape'=> false)); ?></li>
                                                    <?php if($direction == 1) { ?>
                                                        <li><?php echo $this->Form->postLink(__('<i class="fa fa-print"></i> Print Shipping Label'), array('controller' => 'pdfs', 'action' => 'shiplabelee', $shipment['Shipment']['id']), array('escape'=> false)); ?></li>
                                                    <?php } ?>
                                                    <?php if($shipment['Shipment']['status_id'] == 15 || $shipment['Shipment']['status_id'] == 16) { ?>
                                                        <?php if($direction == 1) { ?>
                                                        <li><?php echo $this->Html->link(__('<i class="fa fa-cube"></i> Issue'), array('plugin' => false, 'controller'=> 'orders_lines','action' => 'issue', $shipment['Shipment']['order_id']),array('escape'=> false)); ?></li>
                                                        <?php } else { ?>
                                                        <li><?php echo $this->Html->link(__('<i class="fa fa-cube"></i> Receive'), array('plugin' => false, 'controller'=> 'orders_lines','action' => 'receive', $shipment['Shipment']['order_id']),array('escape'=> false)); ?></li>
                                                        <?php } ?>
                                                    <?php } ?>
                                                <?php } ?>
                                            </ul>
                                        </div>

                                    </td>
                                    </tr>
                                    <?php }*/ ?>
                                </tbody>
                            </table>
                               
                        </div>
                        <pagination max-size="10" total-items="totalItems" ng-model="currentPage" ng-change="pageChanged()" items-per-page="limit"></pagination>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PAGE CONTENT-->
    </div>
</div>
<!-- END CONTENT -->

<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>

    var prev = 10;
    var networks = <?php echo json_encode($networks) ?>;

    var direction_id = <?php echo $direction; ?>;

    var status_id = [];
    var createdfrom = '';
    var tracking_number = '';
    

    var limit = '<?php echo (isset($this->params['named']['limit'])?$this->params['named']['searchby']: $limit); ?>';
    var userUid = '<?php echo $authUser['id']; ?>';

    $(document).ready(function() {

        $('#ShipmentStatusId').select2({
            placeholder: 'Select..',
            minimumResultsForSearch: -1
        });

        $('select.limit').select2({
            minimumResultsForSearch: -1,
            width: '80px'
        });
    });
<?php $this->Html->scriptEnd(); ?>
<?php echo $this->Html->script('/app/Shipments/index.js?v=0.0.1', array('block' => 'pageBlock')); ?>

<?php function ang($str) {
    $str = str_replace('%7B%7B', '{{', $str);
    $str = str_replace('%7D%7D', '}}', $str);
    return $str;
}?>