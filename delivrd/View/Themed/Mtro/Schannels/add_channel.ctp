<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">
                <i class="fa fa-th" style="font-size:18px"></i>
                Add Sales Channel
            </h4>
        </div>
        <?php echo $this->Form->create('Schannel', array('class' => 'form-horizontal', 'ng-submit' => 'addChannel(($event))')); ?>
        <div class="modal-body">
            <div id="modalFormMsg" class="alert alert-danger hide"></div>
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo __('Name'); ?><span class="required">* </span></label>
                <div class="col-md-8">
                    <?php echo $this->Form->input('name', array('class' => 'form-control', 'label' => false)); ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label col-md-3"><?php echo __('URL'); ?></label>
                <div class="col-md-8">
                    <?php echo $this->Form->input('url', array('class' => 'form-control', 'label' => false)); ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default"  ng-click="close($event)">Close</button>
            <button type="submit" class="btn btn-primary">Save Sales Channel</button>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>