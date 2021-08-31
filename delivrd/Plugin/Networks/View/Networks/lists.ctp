<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">

        <?php echo $this->Session->flash(); ?>

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-sitemap"></i> Networks
                        </div>
                        <div class="actions">
                            <?php echo $this->Html->link(__('<i class="fa fa-plus"></i> Add Network'), array('controller'=> 'networks','action' => 'create'),array('escape'=> false, 'class' => 'btn default')); ?>
                        </div>
                    </div>

                    <div class="portlet-body">
                        
                        <div class="table-container">
                            <table class="table table-striped table-bordered table-hover no-footer">
                                <thead>
                                    <tr role="row" class="heading">
                                        <th>Title</th>
                                        <th>Your Role</th>
                                        <th>Satus</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($networks as $network) { ?>
                                    <tr>
                                        <td><?php echo h($network['Network']['name']); ?></td>
                                        <td><?php echo $this->Network->role($network['NetworksUser']['role']); ?></td>
                                        <td><?php echo $this->Network->status($network['NetworksUser']['status']); ?></td>
                                        <td>
                                            <?php echo $this->Html->link('View', array('controller'=>'networks', 'action'=>'view', $network['Network']['id']),array('class'=>'btn btn-sm btn-info')); ?>
                                            <?php echo $this->Html->link('Leave', array('controller'=>'networks', 'action'=>'leave', $network['Network']['id']),array('class'=>'btn btn-sm btn-danger')); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>