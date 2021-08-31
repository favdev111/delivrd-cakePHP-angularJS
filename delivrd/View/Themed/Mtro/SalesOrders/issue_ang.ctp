<style type="text/css">
    .remark-icon{
        font-size: 20px;
        margin-top: 12px;
        margin-left: 24px;
    }
</style>

<!-- BEGIN CONTENT -->
<div class="page-content-wrapper" ng-controller="issueController as issue">
    <div class="page-content">

        <!-- BEGIN PAGE HEADER-->
        <div id="flashMessage"></div>
        <!-- END PAGE HEADER-->

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <!-- Begin: life time stats -->
                <div class="portlet box red-flamingo">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-arrow-left"></i> Issue Sales Order Products
                        </div>
                        <div class="actions">
                            <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>
                            <?php if($is_write) { ?>
                            <div class="btn-group pull-right" style="margin-left: 10px">
                                <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-h"></i>
                                </button>
                                <ul class="dropdown-menu pull-right" role="menu">
                                    <li><?php echo $this->Html->link('<i class="fa fa-search"></i> Order Details', array('controller' => 'salesorders','action' => 'details', $order['Order']['id']), array('escape'=> false)); ?></li>
                                    <li><?php echo $this->Html->link('<i class="fa fa-arrow-left"></i> Issue All Products', array('controller' => 'orders_lines','action' => 'sendalllines', $order['Order']['id']), array('escape'=> false)); ?></li>
                                    <li><?php echo $this->Form->postLink(__('<i class="fa fa-flag-checkered"></i> Complete Order Processing'), array('controller' => 'orders','action' => 'complete', $order['Order']['id']), array('escape'=> false)); ?></li>
                                </ul>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <?php echo $this->Form->create('OrdersLine', array(
                                'class' => 'form-horizontal',
                                'url' => array_merge(array('action' => 'find'), $this->params['pass'])
                                )); 
                                $product_name = (!empty($this->request->params['named']['searchby'])) ? $this->request->params['named']['searchby'] : ''; 
                        ?>
                            <div class="row" style="margin-bottom: 24px;margin-top: 18px;">
                                <div class="col-md-5">
                                    <div class="input-group col-md-12"> 
                                        <?php echo $this->Form->input('searchby', array('label' => false, 'class'=>'code-scan form-control input-md', 'placeholder' => 'Search by SKU,EAN or product name', 'value' => $product_name, 'id' => 'autocomplete', 'autofocus' => 'autofocus', 'ng-scanner-detect'=>"scan_options")); ?>
                                        <span class="input-group-addon">
                                            <i class="fa fa-barcode"></i>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-md blue filter-submit margin-bottom"><i class="fa fa-search"></i></button>
                                    <?php echo $this->html->link('<i class="fa fa-undo"></i>', array('plugin' => false, 'controller' => 'orders_lines', 'action' => 'find', $this->params['pass'][0]), array('class' => 'btn btn-md blue filter-submit margin-bottom', 'escape' => false)); ?>
                                    <a class="btn btn-default skuCode" ng-click="testScanner('ERR0010020030')" href="#" data-sku="ERR0010020030">TEST Scanner</a>
                                </div>
                            </div>
                        <?php  echo $this->Form->end(); ?>
                        <div class="table-container">
                            <table ng-table ="issue.tableParams" class="table table-hover">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th width="5%">#</th>
                                        <th width="8%">Line #</th>
                                        <th width="10%">SKU</th>
                                        <th width="25%">Product Name</th>
                                        <th width="25%" class="img_column hide">Product Image</th>
                                        <th width="10%">Ordered Qty.</th>
                                        <th width="10%">Issue Qty.</th>
                                        <th width="10%">Location</th>
                                        <th width="5%">Remarks</th>
                                        <th width="10%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr ng-repeat="line in $data | orderBy:sortType:sortReverse" id="line_{{line.OrdersLine.id}}" ng-if="line.OrdersLine.type != '4'">
                                        <td><a href="<?php echo $this->Html->url(array('controller' => 'salesorders', 'action' => 'details')); ?>/{{line.OrdersLine.order_id}}">{{line.OrdersLine.order_id}}</a></td>
                                        <td>{{line.OrdersLine.line_number}}</td>
                                        <td><a href="#" ng-click="testScanner(line.OrdersLine.sku)">{{line.OrdersLine.sku}}</a></td>
                                        <td><a href="<?php echo $this->Html->url(array('controller' => 'salesorders', 'action' => 'details')); ?>/{{line.OrdersLine.product_id}}">{{line.Product.name}}</a></td>
                                        <td class="img_column hide"></td>
                                        <td>{{line.OrdersLine.quantity}}</td>
                                        <td><?php echo $this->Form->input('sentqty',array( 'label' => false, 'class' => 'form-control', 'min' => 0, 'required' => 'true', 'value' => '{{line.OrdersLine.sentqty}}', 'required')); ?></td>
                                        <td><?php echo $this->Form->input('warehouse_id', array( 'label' => false, 'class' => 'form-control input-sm', 'div' =>false,'required', 'value' => '{{line.OrdersLine.warehouse_id}}')); ?></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End: life time stats -->
            </div>
            <!-- END PAGE CONTENT-->
        </div>
    </div>
</div>
<!-- END CONTENT -->


<audio id="scannerSuccess">
    <source src="<?php echo $this->webroot; ?>media/barcode-scanner-beep.mp3" type="audio/mpeg">
</audio>
<audio id="scannerError">
    <source src="<?php echo $this->webroot; ?>media/glitch-error.mp3" type="audio/mpeg">
</audio>

<script type="text/javascript">
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    
    var app = angular.module('delivrd-app',['ngTable']); //, 'scanner.detection'

    app.controller('issueController', ['$scope', '$http','NgTableParams', function ($scope, $http, NgTableParams) {
        $scope.order = <?php echo json_encode($order); ?>;
        $scope.order_list = <?php echo json_encode($order_list); ?>;
        //$scope.location = 0;
        //$scope.is_cum_qty = 1;
        var self = this;

        self.tableParams = new NgTableParams({
            page: 1,
            count: 10,
            filter: {
                location: 0,
                q: ''
            }
        }, {
            counts: [],
            getData: function(params) {
                var url = '<?php echo $this->Html->url(['controller'=>'salesorders', 'action' => 'issue_ajax', $order['Order']['id']]); ?>';
                url = url +'/page:'+params.page();

                return $http({
                    method  : 'GET',
                    url     : url,
                }).then(function(data) {

                    // This not must be here
                    // Here is bug if we remove hook below page navigation start duplicate
                    if(data.data.rows_count != params.count()) {
                        //console.log('Remove on pagination bar');
                        prev = data.data.rows_count;
                        $('.ng-table-pager').eq(0).remove();
                    }
                    if(prev != data.data.rows_count) {
                        //console.log('Remove2 on pagination bar');
                        $('.ng-table-pager').eq(0).remove();
                        prev = params.count();
                    }
                    // End Hook

                    params.total(data.data.recordsTotal);
                    return data.data.rows;
                });
            }
        });

        /*$scope.scan_options = {
            onComplete: function(barcode, qty){
                $('#autocomplete').val(barcode);
                if($scope.order_list[barcode] == 'undefined') {
                    $('#scannerError')[0].play();
                } else {
                    $('#scannerSuccess')[0].play();
                }
            }, //or false 
            onError: function(){}, //or false 
            onReceive: function(){}, //or false 
            timeBeforeScanTest: 100,
            avgTimeByChar: 30,
            minLength: 6,
            endChar: [9, 13],
            startChar: [],
            scanButtonKeyCode: false, 
            scanButtonLongPressThreshold: 3,
            onScanButtonLongPressed: function(){} //or false
        };*/

        $scope.testScanner = function(sku) {
            $('#autocomplete').scannerDetection(sku);
            return false;
        };

        $scope.applySearch = function() {
            //console.log($scope.selectedItem);
            self.tableParams.filter({ location: $scope.selectedItem, q: $scope.query });
        }
    }]);

$(document).ready(function(){
    <?php /*if($show_img_column) { ?>
        $('.img_column').removeClass('hide');
    <?php }*/ ?>
    /*$('.receive_order').submit(function(){
        var $form = $(this);
        var formData = $form.serialize();
        $.ajax({
            method: 'POST',
            url: siteUrl + "orders_lines/receivelines/",
            data: formData,
            datatype:'json',
        }).success(function (data) {
            var response = jQuery.parseJSON(data);
            if(response.status == 'confirm') {
                // Show confirm modal
                confirm_negative($form.find('input.lineId').val(), $form.parents('tr').find('#OrdersLineWarehouseId').val(), $form.parents('tr').find('#OrdersLineSentqty').val());
            } else {
                //var messageType = (response.status == true) ? 'success' : 'danger';
                var message = '<div class="alert alert-' + response.status + '" id="msg"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>' + response.message + '</div>';
                $('#flashMessage').html(message);
                setTimeout(function() {
                    $('#msg').hide(1000);
                }, 1000);
            }
        });
        return false;
    });*/

    $('#autocomplete').scannerDetection({
        timeBeforeScanTest: 200, // wait for the next character for upto 200ms
        startChar: [120], // Prefix character for the cabled scanner (OPL6845R)
        endChar: [13], // be sure the scan is complete if key 13 (enter) is detected
        avgTimeByChar: 40, // it's not a barcode if a character takes longer than 40ms
        onComplete: function(barcode, qty){
            /*$scope.$apply(function(){
                $('#autocomplete').val(barcode);
                if($scope.order[barcode] == undefined) {
                    $('#scannerError')[0].play();
                } else {
                    $('#scannerSuccess')[0].play();
                }
            });*/
        }
    });

    /*$('.skuCode').click(function() {
        var sku = $(this).data('sku');
        $scope.$apply(function(){
            $('#autocomplete').val(barcode);
        });
        //var sku = $(this).data('sku');
        //alert(sku);
        //$('#autocomplete').scannerDetection(sku);
        //return false;
    });*/

    function confirm_negative($lineId, warehouse_id, quantity) {
        $('#confirmModal').modal('show').find('.modal-dialog').load('<?php echo $this->Html->url(array('controller'=>'salesorders', 'action'=>'send_line2')); ?>/'+$lineId+'/'+warehouse_id+'/'+quantity);
    }
});
<?php $this->Html->scriptEnd(); ?>
</script>

<div class="modal fade" id="remarks-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Remarks</h4>
            </div>
            <?php echo $this->Form->create(); ?>
            <div class="modal-body release-btns">
                <?php echo $this->Form->textarea('receivenotes',array('value' => '', 'label' => false, 'class' => 'form-control receivenotes', 'id' => '')); ?>
            </div>
            <div class="modal-footer">
                <button type="submit" id="form-remarks" class="btn btn-md blue" data-dismiss="modal">Save</button>
            </div>
            <?php echo $this->Form->end(); ?>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"><div class="modal-dialog modal-md" role="document"></div></div>