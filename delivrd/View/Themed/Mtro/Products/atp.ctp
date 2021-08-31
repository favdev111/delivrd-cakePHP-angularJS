<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <div class="portlet box delivrd">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-flag"></i>
                            Available to Promise Report
                        </div>
                        <div class="actions">
                            <button type="button" name="back" onclick="goBack()" class="btn default yellow-stripe"><i class="fa fa-angle-left"></i> Back</button>

                            <?php if($product['Product']['user_id'] == $this->Session->read('Auth.User.id')) { ?>
                                <div class="btn-group pull-right" style="margin-left: 10px;">
                                    <button type="button" class="btn btn-fit-height green dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
                                    <i class="fa fa-ellipsis-h"></i>
                                    </button>
                                    <ul class="dropdown-menu pull-right" role="menu">
                                        <li><?php echo $this->Html->link(__('<i class="fa fa-search"></i> View'), array('action' => 'view',$product['Product']['id']),array('escape'=> false));  ?></li> 
                                        <li><?php echo $this->Html->link(__('<i class="fa fa-pencil"></i> Edit'), array('action' => 'edit',$product['Product']['id']),array('escape'=> false));  ?></li> 
                                        <li><?php echo $this->Form->postLink(__('<i class="fa fa-trash-o"></i> Delete'), array('action' => 'delete', $product['Product']['id']),array('escape'=> false), __('Are you sure you want to delete %s?', h($product['Product']['name']))); ?></li>
                                        <li><?php echo $this->Html->link(__('<i class="fa fa-history"></i> Transactions History'), array('controller'=> 'inventories','action' => 'transactions_history', $product['Product']['id']),array('escape'=> false)); ?></li>

                                        <li class="divider"></li>
                                    
                                        <li><?php echo $this->Form->postLink(__('<i class="fa fa-lock"></i> Block For Selling'), array('action' => 'changestatus', $product['Product']['id'],12),array('escape'=> false)); ?></li>
                                        <li><?php echo $this->Form->postLink(__('<i class="fa fa-shield"></i> Complete Block'), array('action' => 'changestatus', $product['Product']['id'],13),array('escape'=> false)); ?></li>
                                        <li><?php echo $this->Form->postLink(__('<i class="fa fa-unlock"></i> Cancel Block'), array('action' => 'changestatus', $product['Product']['id'],1),array('escape'=> false)); ?></li>

                                        <li class="divider"></li>
                                        
                                        <li><?php echo $this->Html->link(__('<i class="fa fa-globe"></i> Search for Similar on Amazon'),'http://www.amazon.com/s/ref=nb_sb_noss_2?field-keywords='.h($product['Product']['name']),array('target' => '_blank','escape'=> false));  ?></li>
                                        <li><?php echo $this->Html->link(__('<i class="fa fa-globe"></i> Search for Similar on AliExp'),'http://www.aliexpress.com/wholesale?SearchText='.h($product['Product']['name']),array('target' => '_blank','escape'=> false));  ?></li>
                                    </ul>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    
                    <?php $reorder_point = (!empty($product['Product']['reorder_point'])?$product['Product']['reorder_point']:0); ?>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="thumbnail">
                                    <img class="productImage" rel="product_img" data-id="<?php echo $product['Product']['id']; ?>" src="<?php echo h($product['Product']['imageurl']); ?>" height="128px" width="128px" >
                                </div>
                            </div>
                            <div class="col-md-9">
                                <h4>
                                    <a href="<?php echo $this->Html->url(array('controller'=> 'Products','action' => 'view','product_id' => $product['Product']['id'])); ?>" ><?php echo h($product['Product']['name']); ?></a>
                                </h4>
                                <div><strong>SKU: </strong><?php echo h($product['Product']['sku']); ?></div>
                                <div><strong>Current in stock: </strong><?php echo h($inventory['Inventory']['total']); ?></div>
                                <div><strong>Reorder Point: </strong><?php echo h($reorder_point); ?></div>
                                <p class="margin-top-10"><?php echo h($product['Product']['description']); ?>
                            </div>
                        </div>
                    	<div class="tab-content no-space">
                            <?php echo $this->Form->create('Forecast', array('class' => 'form-horizontal','novalidate' => true)); ?>
                            <div class="text-right">
                                <a href="<?php echo $this->Html->url(array('controller' => 'products', 'action' => 'atp_import', $product['Product']['id'])); ?>" target="_blank" class="btn grey-salsa exportATP"><i class="fa fa-download" aria-hidden="true"></i> Export to CSV</a>
                                <button class="btn green saveForecast"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save Forecast</button>
                            </div>
                            <div class="tab-pane active" id="tab_thistory">
                                <table class="table table-hover no-footer" role="grid">
                                    <thead>
                                        <tr role="row" class="heading">
                                            <th>Month</th>
                                            <th>Receipts</th>
                                            <th>Demand</th>
                                            <th>ATP</th>
                                            <th>Expected Spend</th>
                                            <th>Total Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $start = intval($inventory['Inventory']['total']); ?>
                                        <?php foreach ($lines as $month => $line) { ?>
                                            <?php if(date('Y-m', strtotime($month)) == date('Y-m')) { ?>
                                                <?php $cur_month_forecast = round(((date('t') - date('j'))/date('t')) * intval($line['Forecast']['value'])); ?>
                                            <?php } else { ?>
                                                <?php $cur_month_forecast = intval($line['Forecast']['value']); ?>
                                            <?php } ?>
                                            <?php $start = ($start - $cur_month_forecast + $line['OrdersLine']['total_received']);?>
                                            <tr class="report_line <?php echo (date('Y-m', strtotime($month)) == date('Y-m'))?'bg-success':'';?>">
                                                <td class="month_date">
                                                    <?php #if($start < $cur_month_forecast) { ?>
                                                    <?php if($start < $reorder_point) { ?>
                                                        <div style="display: inline-block;width:20px;"><i class="fa fa-exclamation-triangle text-danger"></i></div>
                                                    <?php } else { ?>
                                                        <div style="display: inline-block;width:20px;"></div>
                                                    <?php } ?>
                                                    <?php echo h($line['OrdersLine']['month']); ?>
                                                </td>
                                                <td>
                                                    <a href="<?php echo $this->Html->url(['controller' => 'OrdersLines', 'action' => 'linereport', $product['Product']['id'], $month]) ?>" data-toggle="modal" data-target="#ajaxModal" rel="modal-lg"><span class="total_received"><?php echo h($line['OrdersLine']['total_received']); ?></span></a>
                                                </td>
                                                <td>
                                                    <input class="forecast_input" type="number" name="forecast[<?php echo h($line['OrdersLine']['month']); ?>]" size="4" value="<?php echo h($cur_month_forecast); ?>">
                                                </td>
                                                <td>
                                                    <?php #if($start < $cur_month_forecast) { ?>
                                                    <?php if($start < $reorder_point) { ?>
                                                        <span class="text-danger atp_val"><?php echo $start; ?></span>
                                                    <?php } else { ?>
                                                        <span class=" atp_val"><?php echo $start; ?></span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php echo h($this->Session->read('currencyname')); ?> <?php echo h($line['OrdersLine']['total_value']); ?>
                                                </td>
                                                <td>
                                                    <?php echo h($this->Session->read('currencyname')); ?> <?php echo abs($start * $line['OrdersLine']['value']); ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-right">
                                <a href="<?php echo $this->Html->url(array('controller' => 'products', 'action' => 'atp_import', $product['Product']['id'])); ?>" target="_blank" class="btn grey-salsa exportATP"><i class="fa fa-download" aria-hidden="true"></i> Export to CSV</a>
                                <button class="btn green saveForecast"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save Forecast</button> 
                            </div>
                            <?php echo $this->Form->end(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    var def_start = <?php echo intval($inventory['Inventory']['total']); ?>;

    $('#ForecastAtpForm').submit(function(){
        var $form = $('#ForecastAtpForm');
        $.ajax({
            type: 'POST',
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType:'json',
            success:function (r, status) {
                toastr[r.action](r.message);
            }
        });
        return false;
    });
    
    $('.forecast_input').change(function(){
        var start = def_start;
        $('.report_line').each(function(){
            var $row = $(this);
            var forecast_input = parseInt($row.find('.forecast_input').val());
            var total_received = parseInt($row.find('.total_received').html());
            start = start - forecast_input + total_received;
            $row.find('.atp_val').html(start).removeClass('text-danger');
            $row.find('.month_date div').html('');
            //if(start < forecast_input) {
            if(start < 0) {
                $row.find('.atp_val').addClass('text-danger');
                $row.find('.month_date div').html('<i class="fa fa-exclamation-triangle text-danger"></i>');
            }
        });
    });
    $('.forecast_input').keyup(function(){
        var start = def_start;
        $('.report_line').each(function(){
            var $row = $(this);
            var forecast_input = parseInt($row.find('.forecast_input').val());
            var total_received = parseInt($row.find('.total_received').html());
            start = start - forecast_input + total_received;
            $row.find('.atp_val').html(start).removeClass('text-danger');
            $row.find('.month_date div').html('');
            //if(start < forecast_input) {
            if(start < 0) {
                $row.find('.atp_val').addClass('text-danger');
                $row.find('.month_date div').html('<i class="fa fa-exclamation-triangle text-danger"></i>');
            }
        });
    })
<?php $this->Html->scriptEnd(); ?>
</script>