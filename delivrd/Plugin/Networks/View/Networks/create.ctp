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
                            <i class="fa fa-sitemap"></i>Create network
                        </div>
                    </div>

                    <div class="portlet-body">
                        <?php echo $this->Form->create('Network', array('role'=>'form')); ?>
                            <div class="form-group">
                                <?php echo $this->Form->input('name', array('class'=>'form-control input-large', 'div'=>false, 'label'=>'Network Name')); ?>
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <div>
                                    <?php echo $this->Form->input('type', array('class'=>'form-control input-large', 'options'=>array('private'=>'Internal Network', 'public'=>'Public Network'), 'div'=>false, 'label'=>false)); ?>
                                </div>
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

<script>
<?php $this->Html->scriptStart(array('inline' => true, 'block'=>'jsAction')); ?>
    $(document).ready(function(){
        $('#NetworkType').select2({
            minimumResultsForSearch: -1
        });
    });
<?php $this->Html->scriptEnd(); ?>
</script>