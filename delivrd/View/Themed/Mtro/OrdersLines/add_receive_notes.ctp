<div class="modal-dialog modal-md">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close" ng-click="close($event)"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><i class="fa fa-edit"></i> Remarks</h4>
        </div>
        <?php echo $this->Form->create('OrdersLine', ['ng-submit' => 'addNote($event)', 'role'=>'form']); ?>
        <div class="modal-body">
            <div class="alert alert-danger hide" id="modalFormMsg"></div>
            <?php echo $this->Form->hidden('id'); ?>
            <?php echo $this->Form->textarea('receivenotes', array('label' => false, 'class' => 'form-control receivenote', 'div' => false)); ?>
        </div>
        <div class="modal-footer">
            <button type="submit" id="form-remarks" class="btn btn-md blue" data-dismiss="modal">Save</button>
        </div>
        <?php echo $this->Form->end(); ?>
    </div>
</div>