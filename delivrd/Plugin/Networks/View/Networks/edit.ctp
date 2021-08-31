<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">

        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <?php echo $this->Session->flash(); ?>
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-sitemap"></i> Network Settings
                        </div>
                    </div>

                    <div class="portlet-body">
                        <?php echo $this->Form->create('Network', array('role'=>'form')); ?>
                            <?php echo $this->Form->hidden('id'); ?>
                            <div class="form-group">
                                <?php echo $this->Form->input('name', array('class'=>'form-control input-large', 'div'=>false, 'label'=>'Network Name')); ?>
                            </div>
                            <div class="form-group">
                                <?php echo $this->Form->input('type', array('class'=>'form-control input-large', 'options'=>array('private'=>'Private Network', 'public'=>'Public Network'), 'div'=>false, 'label'=>'Type')); ?>
                            </div>
                            <div class="form-group">
                            <button class="btn btn-primary">Submit</button>
                            </div>
                        <?php echo $this->Form->end(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>